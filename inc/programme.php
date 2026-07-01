<?php
/**
 * Events hub programme surfaces — lifecycle timeline and router trust narrative.
 *
 * @package msrevents
 */

/**
 * Hub programme lifecycle phases (demo).
 *
 * @return array<string, array{label: string, description: string}>
 */
function msrevents_get_programme_phases() {
	return array(
		'announced'    => array(
			'label'       => __( 'Programme announced', 'msrevents' ),
			'description' => __( 'Season dates, featured programmes, and routing to Awards and Seminars subsites are published on the hub.', 'msrevents' ),
		),
		'registration' => array(
			'label'       => __( 'Registration open', 'msrevents' ),
			'description' => __( 'Delegates and partners can register for highlighted events while programme stories stay current on the hub.', 'msrevents' ),
		),
		'live'         => array(
			'label'       => __( 'Live programme', 'msrevents' ),
			'description' => __( 'Ceremony and seminar activity is in progress — event pages and stories reflect live programme status.', 'msrevents' ),
		),
		'recap'        => array(
			'label'       => __( 'Recap published', 'msrevents' ),
			'description' => __( 'Highlights, winner coverage, and Atlas Briefing resources extend the programme beyond the live window.', 'msrevents' ),
		),
	);
}

/**
 * Active programme phase slug.
 *
 * @return string
 */
function msrevents_get_programme_phase() {
	$phases  = msrevents_get_programme_phases();
	$default = 'live';
	$stored  = sanitize_key( (string) get_option( 'msr_events_programme_phase', $default ) );

	return isset( $phases[ $stored ] ) ? $stored : $default;
}

/**
 * Label for a lifecycle phase slug.
 *
 * @param string $slug Phase slug.
 * @return string
 */
function msrevents_get_lifecycle_phase_label( $slug ) {
	$phases = msrevents_get_programme_phases();
	$slug   = sanitize_key( $slug );

	return isset( $phases[ $slug ] ) ? $phases[ $slug ]['label'] : '';
}

/**
 * Description copy for a lifecycle phase slug.
 *
 * @param string $slug Phase slug.
 * @return string
 */
function msrevents_get_lifecycle_phase_description( $slug ) {
	$phases = msrevents_get_programme_phases();
	$slug   = sanitize_key( $slug );

	return isset( $phases[ $slug ] ) ? $phases[ $slug ]['description'] : '';
}

/**
 * Lifecycle phase for an event post (per-event meta, slug map, then hub default).
 *
 * @param int $post_id Event post ID.
 * @return string Phase slug.
 */
function msrevents_get_event_lifecycle_phase( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : (int) get_the_ID();
	$phases  = msrevents_get_programme_phases();

	$meta = sanitize_key( (string) get_post_meta( $post_id, '_msr_event_lifecycle_phase', true ) );
	if ( $meta && isset( $phases[ $meta ] ) ) {
		return $meta;
	}

	$slug_map = array(
		'msrawards'   => 'live',
		'msrseminars' => 'registration',
	);
	$post_slug = sanitize_key( (string) get_post_field( 'post_name', $post_id ) );
	if ( isset( $slug_map[ $post_slug ] ) ) {
		return $slug_map[ $post_slug ];
	}

	return msrevents_get_programme_phase();
}

/**
 * Compact lifecycle badge for cards and singles.
 *
 * @param string $phase_slug Phase slug.
 * @param string $prefix     Visible prefix before phase label.
 * @return void
 */
function msrevents_render_lifecycle_badge( $phase_slug, $prefix = '' ) {
	$label = msrevents_get_lifecycle_phase_label( $phase_slug );
	if ( '' === $label ) {
		return;
	}

	$prefix = $prefix ? $prefix : __( 'Event status', 'msrevents' );
	?>
	<div class="events-lifecycle-badge events-lifecycle-badge--<?php echo esc_attr( sanitize_key( $phase_slug ) ); ?>" role="status">
		<span class="events-lifecycle-badge__prefix"><?php echo esc_html( $prefix ); ?>:</span>
		<span class="events-lifecycle-badge__label"><?php echo esc_html( $label ); ?></span>
	</div>
	<?php
}

/**
 * Lifecycle badge for the current event in the loop.
 *
 * @param int $post_id Event post ID.
 * @return void
 */
function msrevents_render_event_lifecycle_badge( $post_id = 0 ) {
	msrevents_render_lifecycle_badge( msrevents_get_event_lifecycle_phase( $post_id ) );
}

/**
 * Compact lifecycle band for archive and listing surfaces.
 *
 * @return void
 */
function msrevents_render_programme_lifecycle_band() {
	$phase = msrevents_get_programme_phase();
	$label = msrevents_get_lifecycle_phase_label( $phase );
	$copy  = msrevents_get_lifecycle_phase_description( $phase );
	if ( '' === $label ) {
		return;
	}
	?>
	<section class="events-lifecycle-band msr-reveal" aria-labelledby="events-lifecycle-band-heading">
		<h2 id="events-lifecycle-band-heading" class="visually-hidden"><?php esc_html_e( 'Programme lifecycle', 'msrevents' ); ?></h2>
		<p class="events-lifecycle-band__eyebrow"><?php esc_html_e( 'Programme lifecycle', 'msrevents' ); ?></p>
		<p class="events-lifecycle-band__status">
			<?php
			printf(
				/* translators: %s: current hub programme phase label */
				esc_html__( 'Current hub phase: %s', 'msrevents' ),
				esc_html( $label )
			);
			?>
		</p>
		<?php if ( $copy ) : ?>
			<p class="events-lifecycle-band__copy mb-0"><?php echo esc_html( $copy ); ?></p>
		<?php endif; ?>
	</section>
	<?php
}

/**
 * Programme lifecycle timeline for hub home.
 *
 * @return void
 */
function msrevents_render_programme_timeline() {
	$phases = msrevents_get_programme_phases();
	$active = msrevents_get_programme_phase();
	if ( ! isset( $phases[ $active ] ) ) {
		return;
	}

	$active_label = $phases[ $active ]['label'];
	?>
	<section class="events-programme-timeline msr-reveal" aria-labelledby="events-programme-heading">
		<div class="container">
			<header class="events-programme-timeline__header text-center mb-4">
				<h2 id="events-programme-heading" class="h4 events-programme-timeline__title mb-2">
					<?php esc_html_e( 'Programme lifecycle', 'msrevents' ); ?>
				</h2>
				<p class="events-programme-timeline__status mb-0">
					<?php
					printf(
						/* translators: %s: current programme phase label */
						esc_html__( 'Current phase: %s', 'msrevents' ),
						esc_html( $active_label )
					);
					?>
				</p>
			</header>
			<ol class="events-programme-timeline__list list-unstyled mb-0">
				<?php foreach ( $phases as $slug => $phase ) : ?>
					<?php
					$is_active = ( $slug === $active );
					$item_cls  = 'events-programme-timeline__item';
					if ( $is_active ) {
						$item_cls .= ' is-active';
					}
					?>
					<li class="<?php echo esc_attr( $item_cls ); ?>">
						<div class="events-programme-timeline__marker" aria-hidden="true"></div>
						<div class="events-programme-timeline__body">
							<h3 class="h6 events-programme-timeline__label mb-1"><?php echo esc_html( $phase['label'] ); ?></h3>
							<p class="small events-programme-timeline__copy mb-0"><?php echo esc_html( $phase['description'] ); ?></p>
						</div>
					</li>
				<?php endforeach; ?>
			</ol>
		</div>
	</section>
	<?php
}

/**
 * Programme router trust narrative for stories / events surfaces.
 *
 * @return void
 */
function msrevents_render_programme_router_trust() {
	?>
	<section class="events-programme-trust" aria-labelledby="events-programme-trust-heading">
		<header class="mb-3">
			<h2 id="events-programme-trust-heading" class="h4 events-programme-trust__title mb-2">
				<?php esc_html_e( 'How the hub routes programmes', 'msrevents' ); ?>
			</h2>
			<p class="events-programme-trust__lead mb-0">
				<?php esc_html_e( 'The MSR Events hub connects ceremony, awards, seminars, and publishing surfaces — demonstration copy for portfolio review of a modern events platform.', 'msrevents' ); ?>
			</p>
		</header>
	</section>
	<?php
}
