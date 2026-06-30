<?php
/**
 * Events archive — faceted filters, sort, and shareable query URLs.
 *
 * @package msrevents
 */

/**
 * Permalink for the Our Events page.
 *
 * @return string
 */
function msrevents_get_events_archive_base_url() {
	static $url = null;

	if ( null !== $url ) {
		return $url;
	}

	$page = get_page_by_path( 'our-events' );
	if ( $page instanceof WP_Post ) {
		$url = get_permalink( $page );
		return $url ? (string) $url : home_url( '/our-events/' );
	}

	$url = home_url( '/our-events/' );

	return $url;
}

/**
 * Parsed archive filter state from the request.
 *
 * @return array{format: string, phase: string, sort: string}
 */
function msrevents_get_events_archive_filter_state() {
	$format_slugs = array_keys( msrevents_get_event_format_labels() );
	$phase_slugs  = array_keys( msrevents_get_programme_phases() );

	$format = isset( $_GET['format'] ) ? sanitize_key( (string) wp_unslash( $_GET['format'] ) ) : '';
	$phase  = isset( $_GET['phase'] ) ? sanitize_key( (string) wp_unslash( $_GET['phase'] ) ) : '';
	$sort   = isset( $_GET['sort'] ) ? sanitize_key( (string) wp_unslash( $_GET['sort'] ) ) : 'upcoming';

	if ( ! in_array( $format, $format_slugs, true ) ) {
		$format = '';
	}
	if ( ! in_array( $phase, $phase_slugs, true ) ) {
		$phase = '';
	}
	if ( ! in_array( $sort, array( 'upcoming', 'title' ), true ) ) {
		$sort = 'upcoming';
	}

	return array(
		'format' => $format,
		'phase'  => $phase,
		'sort'   => $sort,
	);
}

/**
 * Build a shareable archive URL with optional filter overrides.
 *
 * @param array{format?: string, phase?: string, sort?: string} $overrides Filter overrides.
 * @return string
 */
function msrevents_events_archive_url( $overrides = array() ) {
	$state = msrevents_get_events_archive_filter_state();

	foreach ( array( 'format', 'phase', 'sort' ) as $key ) {
		if ( array_key_exists( $key, $overrides ) ) {
			$state[ $key ] = sanitize_key( (string) $overrides[ $key ] );
		}
	}

	$args = array();
	if ( '' !== $state['format'] ) {
		$args['format'] = $state['format'];
	}
	if ( '' !== $state['phase'] ) {
		$args['phase'] = $state['phase'];
	}
	if ( 'upcoming' !== $state['sort'] ) {
		$args['sort'] = $state['sort'];
	}

	$base = msrevents_get_events_archive_base_url();

	return $args ? add_query_arg( $args, $base ) : $base;
}

/**
 * Human-readable summary for the active archive filters.
 *
 * @param array{format: string, phase: string, sort: string} $state Filter state.
 * @param int                                                 $count Matching events.
 * @return string
 */
function msrevents_get_events_archive_status_label( $state, $count = 0 ) {
	$parts = array();

	if ( '' !== $state['format'] && function_exists( 'msrevents_get_event_format_label' ) ) {
		$labels = msrevents_get_event_format_labels();
		if ( isset( $labels[ $state['format'] ] ) ) {
			$parts[] = $labels[ $state['format'] ];
		}
	}

	if ( '' !== $state['phase'] ) {
		$phase_label = msrevents_get_lifecycle_phase_label( $state['phase'] );
		if ( $phase_label ) {
			$parts[] = $phase_label;
		}
	}

	if ( 'title' === $state['sort'] ) {
		$parts[] = __( 'A–Z', 'msrevents' );
	} else {
		$parts[] = __( 'Upcoming', 'msrevents' );
	}

	if ( empty( $parts ) ) {
		$parts[] = __( 'All programmes', 'msrevents' );
	}

	$label = implode( ' · ', $parts );

	if ( $count > 0 ) {
		$label .= ' · ' . sprintf(
			/* translators: %d: number of events */
			_n( '%d event', '%d events', $count, 'msrevents' ),
			$count
		);
	}

	return $label;
}

/**
 * Whether an event matches an archive format filter (explicit meta or known slug map only).
 *
 * @param int    $post_id     Event post ID.
 * @param string $format_slug Format slug or empty for all.
 * @return bool
 */
function msrevents_event_matches_archive_format( $post_id, $format_slug ) {
	if ( '' === $format_slug ) {
		return true;
	}

	$labels = msrevents_get_event_format_labels();
	$meta   = sanitize_key( (string) get_post_meta( $post_id, '_msr_event_format', true ) );
	if ( $meta && isset( $labels[ $meta ] ) ) {
		return $meta === $format_slug;
	}

	$slug_map = array(
		'msrawards'   => 'in-person',
		'msrseminars' => 'hybrid',
	);
	$post_slug = sanitize_key( (string) get_post_field( 'post_name', $post_id ) );

	return isset( $slug_map[ $post_slug ] ) && $slug_map[ $post_slug ] === $format_slug;
}

/**
 * Whether an event matches an archive phase filter (explicit meta or known slug map only).
 *
 * @param int    $post_id    Event post ID.
 * @param string $phase_slug Phase slug or empty for all.
 * @return bool
 */
function msrevents_event_matches_archive_phase( $post_id, $phase_slug ) {
	if ( '' === $phase_slug ) {
		return true;
	}

	$phases = msrevents_get_programme_phases();
	$meta   = sanitize_key( (string) get_post_meta( $post_id, '_msr_event_lifecycle_phase', true ) );
	if ( $meta && isset( $phases[ $meta ] ) ) {
		return $meta === $phase_slug;
	}

	$slug_map = array(
		'msrawards'   => 'live',
		'msrseminars' => 'registration',
	);
	$post_slug = sanitize_key( (string) get_post_field( 'post_name', $post_id ) );

	return isset( $slug_map[ $post_slug ] ) && $slug_map[ $post_slug ] === $phase_slug;
}

/**
 * Fetch published events matching archive filters.
 *
 * @param array{format?: string, phase?: string, sort?: string}|null $state Filter state or null for current request.
 * @return WP_Post[]
 */
function msrevents_query_filtered_events( $state = null ) {
	if ( null === $state ) {
		$state = msrevents_get_events_archive_filter_state();
	}

	$query = new WP_Query(
		array(
			'post_type'              => 'event',
			'post_status'            => 'publish',
			'posts_per_page'         => -1,
			'orderby'                => 'title',
			'order'                  => 'ASC',
			'no_found_rows'          => true,
			'update_post_meta_cache' => true,
		)
	);

	$posts = array();

	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			$post_id = get_the_ID();

			if ( ! msrevents_event_matches_archive_format( $post_id, $state['format'] ) ) {
				continue;
			}
			if ( ! msrevents_event_matches_archive_phase( $post_id, $state['phase'] ) ) {
				continue;
			}

			$posts[] = get_post( $post_id );
		}
		wp_reset_postdata();
	}

	if ( 'title' === $state['sort'] ) {
		usort(
			$posts,
			static function ( $a, $b ) {
				return strcasecmp( $a->post_title, $b->post_title );
			}
		);
	} else {
		usort(
			$posts,
			static function ( $a, $b ) {
				$a_time = function_exists( 'msrevents_get_event_calendar_times' )
					? msrevents_get_event_calendar_times( $a->ID )
					: null;
				$b_time = function_exists( 'msrevents_get_event_calendar_times' )
					? msrevents_get_event_calendar_times( $b->ID )
					: null;

				$a_start = $a_time ? (int) $a_time['start'] : PHP_INT_MAX;
				$b_start = $b_time ? (int) $b_time['start'] : PHP_INT_MAX;

				if ( $a_start === $b_start ) {
					return strcasecmp( $a->post_title, $b->post_title );
				}

				return $a_start <=> $b_start;
			}
		);
	}

	return $posts;
}

/**
 * Render format, phase, and sort filter bars for the events archive.
 *
 * @param array{format: string, phase: string, sort: string}|null $state Filter state.
 * @param int|null                                                  $count Precomputed match count.
 * @return void
 */
function msrevents_render_events_archive_filters( $state = null, $count = null ) {
	if ( null === $state ) {
		$state = msrevents_get_events_archive_filter_state();
	}

	if ( null === $count ) {
		$count = count( msrevents_query_filtered_events( $state ) );
	}

	$format_labels = msrevents_get_event_format_labels();
	$phase_labels  = msrevents_get_programme_phases();
	?>
	<div class="events-archive-filters msr-reveal" data-msr-events-archive>
		<div class="events-archive-filters__group events-archive-filters__group--format">
			<p class="events-archive-filters__heading" id="events-archive-format-heading">
				<?php esc_html_e( 'Format', 'msrevents' ); ?>
			</p>
			<?php
			msrevents_filter_bar_open( __( 'Filter events by delivery format', 'msrevents' ) );
			msrevents_filter_bar_link(
				__( 'All formats', 'msrevents' ),
				msrevents_events_archive_url( array( 'format' => '' ) ),
				'' === $state['format']
			);
			foreach ( $format_labels as $slug => $label ) {
				msrevents_filter_bar_link(
					$label,
					msrevents_events_archive_url( array( 'format' => $slug ) ),
					$state['format'] === $slug
				);
			}
			msrevents_filter_bar_close();
			?>
		</div>

		<div class="events-archive-filters__group events-archive-filters__group--phase">
			<p class="events-archive-filters__heading" id="events-archive-phase-heading">
				<?php esc_html_e( 'Programme phase', 'msrevents' ); ?>
			</p>
			<?php
			msrevents_filter_bar_open( __( 'Filter events by programme phase', 'msrevents' ) );
			msrevents_filter_bar_link(
				__( 'All phases', 'msrevents' ),
				msrevents_events_archive_url( array( 'phase' => '' ) ),
				'' === $state['phase']
			);
			foreach ( $phase_labels as $slug => $phase ) {
				msrevents_filter_bar_link(
					$phase['label'],
					msrevents_events_archive_url( array( 'phase' => $slug ) ),
					$state['phase'] === $slug
				);
			}
			msrevents_filter_bar_close();
			?>
		</div>

		<div class="events-archive-filters__group events-archive-filters__group--sort">
			<p class="events-archive-filters__heading" id="events-archive-sort-heading">
				<?php esc_html_e( 'Sort', 'msrevents' ); ?>
			</p>
			<?php
			msrevents_filter_bar_open( __( 'Sort event listings', 'msrevents' ) );
			msrevents_filter_bar_link(
				__( 'Upcoming', 'msrevents' ),
				msrevents_events_archive_url( array( 'sort' => 'upcoming' ) ),
				'upcoming' === $state['sort']
			);
			msrevents_filter_bar_link(
				__( 'A–Z', 'msrevents' ),
				msrevents_events_archive_url( array( 'sort' => 'title' ) ),
				'title' === $state['sort']
			);
			msrevents_filter_bar_close();
			?>
		</div>

		<?php
		msrevents_filter_bar_status( msrevents_get_events_archive_status_label( $state, $count ) );
		?>
	</div>
	<?php
}

/**
 * Render filtered event listings or an empty state.
 *
 * @param array{format: string, phase: string, sort: string}|null $state Filter state.
 * @param WP_Post[]|null                                            $posts Precomputed posts.
 * @return void
 */
function msrevents_render_events_archive_listing( $state = null, $posts = null ) {
	if ( null === $state ) {
		$state = msrevents_get_events_archive_filter_state();
	}

	if ( null === $posts ) {
		$posts = msrevents_query_filtered_events( $state );
	}

	if ( empty( $posts ) ) {
		$has_filters = ( '' !== $state['format'] || '' !== $state['phase'] );

		msrevents_render_empty_state(
			array(
				'context' => 'listing',
				'title'   => $has_filters
					? __( 'No events match these filters', 'msrevents' )
					: __( 'No events listed yet', 'msrevents' ),
				'message' => $has_filters
					? __( 'Try a different format or programme phase, or browse all programmes.', 'msrevents' )
					: __( 'Programme pages will appear here when published.', 'msrevents' ),
				'links'   => $has_filters
					? array(
						array(
							'title' => __( 'Clear filters', 'msrevents' ),
							'url'   => msrevents_events_archive_url( array( 'format' => '', 'phase' => '' ) ),
						),
						array(
							'title' => __( 'Browse topics', 'msrevents' ),
							'url'   => home_url( '/topics/' ),
						),
					)
					: array(),
			)
		);
		return;
	}
	?>
	<div class="row g-4 msr-card-grid events-archive-listing events-archive-listing--events">
		<?php
		global $post;
		foreach ( $posts as $post ) {
			setup_postdata( $post );
			get_template_part( 'template-parts/cards/event-card' );
		}
		wp_reset_postdata();
		?>
	</div>
	<?php
}
