<?php
/**
 * Portfolio demonstration surfaces — stats, featured events, CTA preview copy.
 *
 * @package msrevents
 */

/**
 * Published count for a post type.
 *
 * @param string $post_type Post type slug.
 * @return int
 */
function msrevents_count_published( $post_type ) {
	$counts = wp_count_posts( $post_type );
	if ( ! $counts || ! isset( $counts->publish ) ) {
		return 0;
	}
	return (int) $counts->publish;
}

/**
 * Social proof stats for the events hub home.
 *
 * @return array<int, array{value: string, label: string}>
 */
function msrevents_get_programme_stats() {
	$events  = msrevents_count_published( 'event' );
	$stories = msrevents_count_published( 'post' );
	$podcasts = msrevents_count_published( 'podcast' );

	return array(
		array(
			'value' => '2',
			'label' => __( 'Programmes', 'msrevents' ),
		),
		array(
			'value' => $events > 0 ? (string) $events : '6',
			'label' => __( 'Events', 'msrevents' ),
		),
		array(
			'value' => $stories > 0 ? (string) $stories : '12',
			'label' => __( 'Stories', 'msrevents' ),
		),
		array(
			'value' => $podcasts > 0 ? (string) $podcasts : '4',
			'label' => __( 'Podcasts', 'msrevents' ),
		),
	);
}

/**
 * Featured hub events for home.
 *
 * @param int $limit Max cards.
 * @return array<int, array{title: string, meta: string, url: string, post_id: int, date_iso: string}>
 */
function msrevents_get_featured_events( $limit = 3 ) {
	$query = new WP_Query(
		array(
			'post_type'              => 'event',
			'post_status'            => 'publish',
			'posts_per_page'         => $limit,
			'orderby'                => 'date',
			'order'                  => 'DESC',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		)
	);

	$items = array();
	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			$items[] = array(
				'title'    => get_the_title(),
				'meta'     => get_the_date(),
				'date_iso' => get_the_date( 'c' ),
				'url'      => get_permalink(),
				'post_id'  => get_the_ID(),
			);
		}
		wp_reset_postdata();
	}

	return $items;
}

/**
 * Delivery format label for hero (portfolio default).
 *
 * @return string
 */
function msrevents_get_programme_format_label() {
	$label = (string) apply_filters( 'msrevents_programme_format_label', __( 'Hybrid showcase · portfolio demo', 'msrevents' ) );
	return trim( $label );
}

/**
 * Primary header CTA with portfolio preview note.
 *
 * @return void
 */
function msrevents_render_header_cta() {
	if ( ! function_exists( 'msr_get_primary_cta' ) ) {
		return;
	}

	$cta = msr_get_primary_cta();
	if ( empty( $cta['label'] ) || empty( $cta['url'] ) ) {
		return;
	}

	$events_url = msrevents_get_page_url( 'our-events', '/our-events/' );
	$topics_url = msrevents_get_page_url( 'topics', '/topics/' );
	$main_url   = $events_url ? $events_url : (string) $cta['url'];
	$sub_url    = $topics_url ? $topics_url : $main_url;
	?>
	<div class="msr-primary-cta msr-primary-cta--events">
		<div class="msr-primary-cta__actions">
			<a class="btn btn-primary msr-primary-cta__main" href="<?php echo esc_url( $main_url ); ?>"><?php echo esc_html( (string) $cta['label'] ); ?></a>
			<?php if ( ! empty( $cta['sub'] ) ) : ?>
			<a class="btn btn-outline-primary msr-primary-cta__sub" href="<?php echo esc_url( $sub_url ); ?>"><?php echo esc_html( (string) $cta['sub'] ); ?></a>
			<?php endif; ?>
		</div>
		<p class="msr-primary-cta__preview small mb-0"><?php esc_html_e( 'Preview — ticketing connects at launch', 'msrevents' ); ?></p>
	</div>
	<?php
}

/**
 * Programme stats strip (social proof).
 *
 * @return void
 */
function msrevents_render_programme_stats() {
	$stats = msrevents_get_programme_stats();
	if ( ! $stats ) {
		return;
	}
	?>
	<section class="events-programme-stats msr-reveal" aria-labelledby="events-programme-stats-heading">
		<div class="container">
			<h2 id="events-programme-stats-heading" class="visually-hidden"><?php esc_html_e( 'Programme at a glance', 'msrevents' ); ?></h2>
			<ul class="events-programme-stats__list list-unstyled mb-0">
				<?php foreach ( $stats as $stat ) : ?>
				<li class="events-programme-stats__item">
					<p class="events-programme-stats__value mb-0"><?php echo esc_html( $stat['value'] ); ?></p>
					<p class="events-programme-stats__label mb-0"><?php echo esc_html( $stat['label'] ); ?></p>
				</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</section>
	<?php
}

/**
 * Featured events band for programme home.
 *
 * @return void
 */
function msrevents_render_featured_events() {
	$events = msrevents_get_featured_events( 3 );
	if ( ! $events ) {
		return;
	}
	$archive_url = msrevents_get_page_url( 'our-events', '/our-events/' );
	?>
	<section class="events-featured-events msr-reveal" aria-labelledby="events-featured-events-heading">
		<div class="container">
			<header class="events-featured-events__header text-center mb-4">
				<h2 id="events-featured-events-heading" class="h4 events-featured-events__title mb-2">
					<?php esc_html_e( 'Featured events', 'msrevents' ); ?>
				</h2>
				<p class="events-featured-events__lead mb-0">
					<?php esc_html_e( 'A sample of programme listings from the seeded hub archive — swap for live season picks before launch.', 'msrevents' ); ?>
				</p>
			</header>
			<ul class="events-featured-events__grid list-unstyled mb-0">
				<?php foreach ( $events as $event ) : ?>
				<li class="events-featured-events__item panel">
					<div class="events-featured-events__card">
						<a class="events-featured-events__card-link" href="<?php echo esc_url( $event['url'] ); ?>">
							<h3 class="h6 events-featured-events__event-title mb-1"><?php echo esc_html( $event['title'] ); ?></h3>
						</a>
						<?php if ( ! empty( $event['post_id'] ) && function_exists( 'msrevents_render_event_meta_chips' ) ) : ?>
							<?php msrevents_render_event_meta_chips( (int) $event['post_id'] ); ?>
						<?php elseif ( ! empty( $event['meta'] ) ) : ?>
						<p class="small events-featured-events__meta mb-0">
							<time datetime="<?php echo esc_attr( $event['date_iso'] ); ?>"><?php echo esc_html( $event['meta'] ); ?></time>
						</p>
						<?php endif; ?>
					</div>
				</li>
				<?php endforeach; ?>
			</ul>
			<?php if ( $archive_url ) : ?>
			<div class="events-featured-events__cta events-ctas">
				<a class="btn btn-outline-primary events-featured-events__archive-btn" href="<?php echo esc_url( $archive_url ); ?>"><?php esc_html_e( 'Browse all events', 'msrevents' ); ?></a>
			</div>
			<?php endif; ?>
		</div>
	</section>
	<?php
}

add_filter(
	'msr_primary_cta',
	static function ( $cta, $ctx ) {
		if ( 'hub' !== $ctx || ! is_array( $cta ) ) {
			return $cta;
		}
		$events_url = msrevents_get_page_url( 'our-events', '/our-events/' );
		if ( $events_url ) {
			$cta['url'] = $events_url;
		}
		$topics_url = msrevents_get_page_url( 'topics', '/topics/' );
		if ( $topics_url ) {
			$cta['sub_url'] = $topics_url;
		}
		return $cta;
	},
	10,
	2
);
