<?php
/**
 * Event single depth — media fallback, mini agenda, partners strip, related programmes.
 *
 * @package msrevents
 */

/**
 * Agenda preview rows for an event (seeded JSON meta).
 *
 * @param int $post_id Event post ID.
 * @return array<int, array{time: string, title: string, format: string}>
 */
function msrevents_get_event_agenda_rows( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : (int) get_the_ID();
	$stored  = get_post_meta( $post_id, '_msr_event_agenda', true );

	if ( is_string( $stored ) && '' !== $stored ) {
		$decoded = json_decode( $stored, true );
		$stored  = is_array( $decoded ) ? $decoded : array();
	}

	if ( ! is_array( $stored ) ) {
		return array();
	}

	$rows = array();
	foreach ( $stored as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}
		$title = trim( (string) ( $row['title'] ?? '' ) );
		if ( '' === $title ) {
			continue;
		}
		$rows[] = array(
			'time'   => trim( (string) ( $row['time'] ?? '' ) ),
			'title'  => $title,
			'format' => trim( (string) ( $row['format'] ?? '' ) ),
		);
	}

	return $rows;
}

/**
 * Deep link to full agenda or programme route on a subsite.
 *
 * @param int $post_id Event post ID.
 * @return string
 */
function msrevents_get_event_agenda_archive_url( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : (int) get_the_ID();
	$stored  = (string) get_post_meta( $post_id, '_msr_event_agenda_url', true );
	if ( '' !== trim( $stored ) ) {
		return esc_url_raw( $stored );
	}

	$slug_map = array(
		'msrseminars' => 'http://msrevents.local:8888/msrseminars/agenda/',
		'msrawards'   => 'http://msrevents.local:8888/msrawards/nominees/',
	);

	$post_slug = sanitize_key( (string) get_post_field( 'post_name', $post_id ) );

	return $slug_map[ $post_slug ] ?? home_url( '/our-events/' );
}

/**
 * Partner post IDs featured on an event single.
 *
 * @param int $post_id Event post ID.
 * @return int[]
 */
function msrevents_get_event_partner_ids( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : (int) get_the_ID();
	$stored  = get_post_meta( $post_id, '_msr_event_partner_ids', true );

	if ( is_string( $stored ) && '' !== $stored ) {
		$parts = array_map( 'intval', explode( ',', $stored ) );
		return array_values( array_filter( $parts ) );
	}

	if ( is_array( $stored ) ) {
		return array_values( array_filter( array_map( 'intval', $stored ) ) );
	}

	return array();
}

/**
 * Related programme cards for cross-routing (excludes current event programme).
 *
 * @param int $post_id Event post ID.
 * @return array<int, array{label: string, description: string, url: string, cta: string}>
 */
function msrevents_get_event_related_programmes( $post_id = 0 ) {
	$post_id   = $post_id ? (int) $post_id : (int) get_the_ID();
	$post_slug = sanitize_key( (string) get_post_field( 'post_name', $post_id ) );
	$links     = function_exists( 'msrevents_get_ecosystem_links' ) ? msrevents_get_ecosystem_links() : array();
	$related   = array();

	foreach ( $links as $link ) {
		$key = $link['key'] ?? '';
		if ( 'msrseminars' === $post_slug && 'seminars' === $key ) {
			continue;
		}
		if ( 'msrawards' === $post_slug && 'awards' === $key ) {
			continue;
		}
		$related[] = array(
			'label'       => (string) ( $link['label'] ?? '' ),
			'description' => (string) ( $link['description'] ?? '' ),
			'url'         => (string) ( $link['url'] ?? '' ),
			'cta'         => (string) ( $link['cta'] ?? __( 'Learn more', 'msrevents' ) ),
		);
	}

	return $related;
}

/**
 * Hero media column — video embed or programme placeholder.
 *
 * @param int $post_id Event post ID.
 * @return void
 */
function msrevents_render_event_single_media( $post_id = 0 ) {
	$post_id  = $post_id ? (int) $post_id : (int) get_the_ID();
	$location = function_exists( 'get_field' ) ? get_field( 'location', $post_id ) : '';
	?>
	<div class="events-single-media">
		<?php if ( $location ) : ?>
			<div class="ratio ratio-16x9 events-single-media__video">
				<?php
				if ( function_exists( 'msrevents_render_video_embed' ) ) {
					echo msrevents_render_video_embed( $location ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				} else {
					msrevents_render_rich_text( $location );
				}
				?>
			</div>
		<?php else : ?>
			<div class="events-single-media__placeholder panel text-center" role="img" aria-label="<?php esc_attr_e( 'Programme preview placeholder', 'msrevents' ); ?>">
				<i class="fa-solid fa-calendar-days events-single-media__placeholder-icon" aria-hidden="true"></i>
				<p class="events-single-media__placeholder-format mb-2">
					<?php echo esc_html( msrevents_get_event_format_label( $post_id ) ); ?>
				</p>
				<p class="events-single-media__placeholder-copy small mb-0">
					<?php esc_html_e( 'Portfolio programme preview — connect venue media or live stream before launch.', 'msrevents' ); ?>
				</p>
			</div>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Mini agenda preview with link to full programme route.
 *
 * @param int $post_id Event post ID.
 * @return void
 */
function msrevents_render_event_mini_agenda( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : (int) get_the_ID();
	$rows    = msrevents_get_event_agenda_rows( $post_id );

	if ( ! $rows ) {
		return;
	}

	$archive_url = msrevents_get_event_agenda_archive_url( $post_id );
	?>
	<section class="events-event-mini-agenda msr-reveal panel" aria-labelledby="events-event-mini-agenda-heading-<?php echo esc_attr( (string) $post_id ); ?>">
		<header class="events-event-mini-agenda__header mb-3">
			<h2 class="h5 events-event-mini-agenda__title mb-1" id="events-event-mini-agenda-heading-<?php echo esc_attr( (string) $post_id ); ?>">
				<?php esc_html_e( 'Agenda preview', 'msrevents' ); ?>
			</h2>
			<p class="events-event-mini-agenda__lead small mb-0">
				<?php esc_html_e( 'Sample schedule rows for portfolio review — full programme detail lives on the linked subsite.', 'msrevents' ); ?>
			</p>
		</header>
		<ol class="events-event-mini-agenda__list list-unstyled mb-3">
			<?php foreach ( $rows as $row ) : ?>
				<li class="events-event-mini-agenda__item">
					<?php if ( $row['time'] ) : ?>
						<time class="events-event-mini-agenda__time"><?php echo esc_html( $row['time'] ); ?></time>
					<?php endif; ?>
					<span class="events-event-mini-agenda__session"><?php echo esc_html( $row['title'] ); ?></span>
					<?php if ( $row['format'] ) : ?>
						<span class="events-event-mini-agenda__format"><?php echo esc_html( $row['format'] ); ?></span>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ol>
		<a class="btn btn-outline-primary events-event-mini-agenda__link" href="<?php echo esc_url( $archive_url ); ?>">
			<?php esc_html_e( 'View full programme', 'msrevents' ); ?>
		</a>
	</section>
	<?php
}

/**
 * Compact partner logos for an event single.
 *
 * @param int $post_id Event post ID.
 * @return void
 */
function msrevents_render_event_partners_strip( $post_id = 0 ) {
	$post_id     = $post_id ? (int) $post_id : (int) get_the_ID();
	$partner_ids = msrevents_get_event_partner_ids( $post_id );

	if ( ! $partner_ids ) {
		return;
	}

	$partners = array();
	foreach ( $partner_ids as $partner_id ) {
		$partner = get_post( (int) $partner_id );
		if ( $partner instanceof WP_Post && 'partner' === $partner->post_type && 'publish' === $partner->post_status ) {
			$partners[] = $partner;
		}
	}

	if ( ! $partners ) {
		return;
	}

	$partners_url = home_url( '/partners/' );
	?>
	<section class="events-event-partners msr-reveal" aria-labelledby="events-event-partners-heading-<?php echo esc_attr( (string) $post_id ); ?>">
		<header class="events-event-partners__header text-center mb-3">
			<h2 class="h5 events-event-partners__title mb-1" id="events-event-partners-heading-<?php echo esc_attr( (string) $post_id ); ?>">
				<?php esc_html_e( 'Delivered with partners', 'msrevents' ); ?>
			</h2>
			<p class="events-event-partners__lead small mb-0">
				<?php esc_html_e( 'Supporters helping deliver this programme — demonstration tier logos for portfolio review.', 'msrevents' ); ?>
			</p>
		</header>
		<div class="events-event-partners__grid d-flex flex-wrap justify-content-center gap-3 mb-3">
			<?php
			global $post;
			foreach ( $partners as $partner_post ) {
				$post = $partner_post; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
				setup_postdata( $post );
				get_template_part(
					'template-parts/cards/partner-card',
					null,
					array( 'compact' => true )
				);
			}
			wp_reset_postdata();
			?>
		</div>
		<div class="text-center">
			<a class="btn btn-sm btn-outline-primary events-event-partners__archive-link" href="<?php echo esc_url( $partners_url ); ?>">
				<?php esc_html_e( 'View all partners', 'msrevents' ); ?>
			</a>
		</div>
	</section>
	<?php
}

/**
 * Cross-links to other MSR programmes on event singles.
 *
 * @param int $post_id Event post ID.
 * @return void
 */
function msrevents_render_event_related_programmes( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : (int) get_the_ID();
	$items   = msrevents_get_event_related_programmes( $post_id );

	if ( ! $items ) {
		return;
	}
	?>
	<section class="events-event-related msr-reveal" aria-labelledby="events-event-related-heading-<?php echo esc_attr( (string) $post_id ); ?>">
		<header class="events-event-related__header text-center mb-3">
			<h2 class="h5 events-event-related__title mb-1" id="events-event-related-heading-<?php echo esc_attr( (string) $post_id ); ?>">
				<?php esc_html_e( 'Related programmes', 'msrevents' ); ?>
			</h2>
			<p class="events-event-related__lead small mb-0">
				<?php esc_html_e( 'Continue exploring the MSR demonstration estate from this event.', 'msrevents' ); ?>
			</p>
		</header>
		<div class="row g-3 justify-content-center">
			<?php foreach ( $items as $item ) : ?>
				<div class="col-md-6">
					<div class="events-event-related__card panel h-100">
						<h3 class="h6 events-event-related__card-title mb-2"><?php echo esc_html( $item['label'] ); ?></h3>
						<p class="small events-event-related__card-copy mb-3"><?php echo esc_html( $item['description'] ); ?></p>
						<a class="btn btn-outline-primary btn-sm" href="<?php echo esc_url( $item['url'] ); ?>">
							<?php echo esc_html( $item['cta'] ); ?>
						</a>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</section>
	<?php
}
