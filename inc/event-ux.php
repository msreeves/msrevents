<?php
/**
 * Modern event UX bands — meta chips, calendar demo, registration countdown, sticky CTA.
 *
 * @package msrevents
 */

/**
 * Delivery format slugs and labels.
 *
 * @return array<string, string>
 */
function msrevents_get_event_format_labels() {
	return array(
		'in-person' => __( 'In person', 'msrevents' ),
		'hybrid'      => __( 'Hybrid', 'msrevents' ),
		'virtual'     => __( 'Virtual', 'msrevents' ),
	);
}

/**
 * Event delivery format slug.
 *
 * @param int $post_id Event post ID.
 * @return string
 */
function msrevents_get_event_format_slug( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : (int) get_the_ID();
	$meta    = sanitize_key( (string) get_post_meta( $post_id, '_msr_event_format', true ) );
	$labels  = msrevents_get_event_format_labels();

	if ( $meta && isset( $labels[ $meta ] ) ) {
		return $meta;
	}

	$slug_map = array(
		'msrawards'   => 'in-person',
		'msrseminars' => 'hybrid',
	);

	$post_slug = sanitize_key( (string) get_post_field( 'post_name', $post_id ) );

	return $slug_map[ $post_slug ] ?? 'hybrid';
}

/**
 * Human-readable format label for an event.
 *
 * @param int $post_id Event post ID.
 * @return string
 */
function msrevents_get_event_format_label( $post_id = 0 ) {
	$labels = msrevents_get_event_format_labels();
	$slug   = msrevents_get_event_format_slug( $post_id );

	return $labels[ $slug ] ?? '';
}

/**
 * Normalized date/time group from ACF.
 *
 * @param int $post_id Event post ID.
 * @return array{date: array{start: string, finish: string}, time: array{start: string, finish: string}, venue_name: string}
 */
function msrevents_get_event_schedule_fields( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : (int) get_the_ID();
	$date    = get_field( 'date', $post_id );
	$time    = get_field( 'time', $post_id );
	$venue   = get_field( 'venue', $post_id );

	if ( ! is_array( $date ) ) {
		$date = array();
	}
	if ( ! is_array( $time ) ) {
		$time = array();
	}
	if ( ! is_array( $venue ) ) {
		$venue = array();
	}

	return array(
		'date'       => array(
			'start'  => isset( $date['start'] ) ? trim( (string) $date['start'] ) : '',
			'finish' => isset( $date['finish'] ) ? trim( (string) $date['finish'] ) : '',
		),
		'time'       => array(
			'start'  => isset( $time['start'] ) ? trim( (string) $time['start'] ) : '',
			'finish' => isset( $time['finish'] ) ? trim( (string) $time['finish'] ) : '',
		),
		'venue_name' => isset( $venue['name'] ) ? trim( (string) $venue['name'] ) : '',
	);
}

/**
 * Compact date label for chips (e.g. "October 14, 2026" or range).
 *
 * @param int $post_id Event post ID.
 * @return string
 */
function msrevents_get_event_date_chip_label( $post_id = 0 ) {
	$schedule = msrevents_get_event_schedule_fields( $post_id );
	$start    = $schedule['date']['start'];
	$finish   = $schedule['date']['finish'];

	if ( '' === $start && '' === $finish ) {
		return '';
	}
	if ( '' === $finish || $start === $finish ) {
		return $start;
	}

	return trim( $start . ' – ' . $finish );
}

/**
 * Compact schedule line for archive cards (date · time · venue).
 *
 * @param int $post_id Event post ID.
 * @return string
 */
function msrevents_get_event_schedule_summary( $post_id = 0 ) {
	$schedule = msrevents_get_event_schedule_fields( $post_id );
	$parts    = array();

	$date_label = msrevents_get_event_date_chip_label( $post_id );
	if ( $date_label ) {
		$parts[] = $date_label;
	}

	$time_start = $schedule['time']['start'];
	$time_end   = $schedule['time']['finish'];
	if ( $time_start || $time_end ) {
		if ( $time_start && $time_end && $time_start !== $time_end ) {
			$parts[] = trim( $time_start . ' – ' . $time_end );
		} else {
			$parts[] = $time_start ? $time_start : $time_end;
		}
	}

	if ( $schedule['venue_name'] ) {
		$parts[] = $schedule['venue_name'];
	}

	return implode( ' · ', array_filter( $parts ) );
}

/**
 * Short plain-text teaser from the event information field.
 *
 * @param int $post_id     Event post ID.
 * @param int $word_count  Max words.
 * @return string
 */
function msrevents_get_event_listing_teaser( $post_id = 0, $word_count = 24 ) {
	$post_id = $post_id ? (int) $post_id : (int) get_the_ID();
	$info    = get_field( 'information', $post_id );
	if ( ! $info ) {
		return '';
	}

	return wp_trim_words( wp_strip_all_tags( (string) $info ), max( 8, (int) $word_count ), '…' );
}

/**
 * Parse ACF display date to timestamp (site timezone).
 *
 * @param string $display_date Date string from ACF (F j, Y).
 * @param string $time_str     Optional time (g:i a).
 * @return int Unix timestamp or 0.
 */
function msrevents_parse_event_datetime( $display_date, $time_str = '' ) {
	$display_date = trim( (string) $display_date );
	if ( '' === $display_date ) {
		return 0;
	}

	$tz = wp_timezone();
	$dt = DateTime::createFromFormat( 'F j, Y', $display_date, $tz );
	if ( ! $dt ) {
		$parsed = strtotime( $display_date );
		return $parsed ? (int) $parsed : 0;
	}

	if ( $time_str ) {
		$time_dt = DateTime::createFromFormat( 'g:i a', trim( (string) $time_str ), $tz );
		if ( $time_dt ) {
			$dt->setTime( (int) $time_dt->format( 'H' ), (int) $time_dt->format( 'i' ) );
		}
	}

	return (int) $dt->getTimestamp();
}

/**
 * Calendar start/end timestamps for an event.
 *
 * @param int $post_id Event post ID.
 * @return array{start: int, end: int}|null
 */
function msrevents_get_event_calendar_times( $post_id = 0 ) {
	$post_id  = $post_id ? (int) $post_id : (int) get_the_ID();
	$schedule = msrevents_get_event_schedule_fields( $post_id );
	$start_ts = msrevents_parse_event_datetime( $schedule['date']['start'], $schedule['time']['start'] );

	if ( ! $start_ts ) {
		return null;
	}

	$end_ts = msrevents_parse_event_datetime(
		$schedule['date']['finish'] ? $schedule['date']['finish'] : $schedule['date']['start'],
		$schedule['time']['finish'] ? $schedule['time']['finish'] : $schedule['time']['start']
	);

	if ( ! $end_ts || $end_ts <= $start_ts ) {
		$end_ts = $start_ts + 2 * HOUR_IN_SECONDS;
	}

	return array(
		'start' => $start_ts,
		'end'   => $end_ts,
	);
}

/**
 * Days until event start (for registration countdown).
 *
 * @param int $post_id Event post ID.
 * @return int|null Days remaining, or null when not computable.
 */
function msrevents_get_event_days_until_start( $post_id = 0 ) {
	$times = msrevents_get_event_calendar_times( $post_id );
	if ( ! $times ) {
		return null;
	}

	$tz    = wp_timezone();
	$now   = new DateTime( 'now', $tz );
	$start = ( new DateTime( '@' . $times['start'] ) )->setTimezone( $tz );
	$start->setTime( 0, 0, 0 );
	$now->setTime( 0, 0, 0 );

	$diff = (int) $now->diff( $start )->format( '%r%a' );

	return max( 0, $diff );
}

/**
 * Render date + format meta chips.
 *
 * @param int    $post_id     Event post ID.
 * @param string $extra_class Optional extra class on the list (e.g. featured card layout).
 * @return void
 */
function msrevents_render_event_meta_chips( $post_id = 0, $extra_class = '' ) {
	$post_id    = $post_id ? (int) $post_id : (int) get_the_ID();
	$date_label = msrevents_get_event_date_chip_label( $post_id );
	$format     = msrevents_get_event_format_label( $post_id );

	if ( '' === $date_label && '' === $format ) {
		return;
	}

	$list_class = 'events-event-meta-chips list-unstyled mb-0';
	$extra      = trim( (string) $extra_class );
	if ( '' !== $extra ) {
		$list_class .= ' ' . preg_replace( '/[^A-Za-z0-9_\-\s]/', '', $extra );
	}
	?>
	<ul class="<?php echo esc_attr( $list_class ); ?>" role="list">
		<?php if ( $date_label ) : ?>
		<li class="events-event-meta-chips__item">
			<span class="events-event-chip events-event-chip--date">
				<i class="fa-solid fa-calendar" aria-hidden="true"></i>
				<span><?php echo esc_html( $date_label ); ?></span>
			</span>
		</li>
		<?php endif; ?>
		<?php if ( $format ) : ?>
		<li class="events-event-meta-chips__item">
			<span class="events-event-chip events-event-chip--format">
				<i class="fa-solid fa-location-dot" aria-hidden="true"></i>
				<span><?php echo esc_html( $format ); ?></span>
			</span>
		</li>
		<?php endif; ?>
	</ul>
	<?php
}

/**
 * Google Calendar URL for an event (portfolio demo).
 *
 * @param int $post_id Event post ID.
 * @return string
 */
function msrevents_get_event_google_calendar_url( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : (int) get_the_ID();
	$times   = msrevents_get_event_calendar_times( $post_id );
	if ( ! $times ) {
		return '';
	}

	$schedule = msrevents_get_event_schedule_fields( $post_id );
	$title    = get_the_title( $post_id );
	$details  = __( 'Portfolio demonstration — add to calendar preview only.', 'msrevents' );
	$location = $schedule['venue_name'] ? $schedule['venue_name'] : get_bloginfo( 'name' );
	$dates    = gmdate( 'Ymd\THis\Z', $times['start'] ) . '/' . gmdate( 'Ymd\THis\Z', $times['end'] );

	return add_query_arg(
		array(
			'action'   => 'TEMPLATE',
			'text'     => $title,
			'dates'    => $dates,
			'details'  => $details,
			'location' => $location,
		),
		'https://calendar.google.com/calendar/render'
	);
}

/**
 * ICS download URL for an event.
 *
 * @param int $post_id Event post ID.
 * @return string
 */
function msrevents_get_event_ics_url( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : (int) get_the_ID();
	$url     = get_permalink( $post_id );

	return $url ? add_query_arg( 'msr_event_ics', (string) $post_id, $url ) : '';
}

/**
 * Build ICS payload for an event.
 *
 * @param int $post_id Event post ID.
 * @return string
 */
function msrevents_build_event_ics( $post_id ) {
	$post_id = (int) $post_id;
	$times   = msrevents_get_event_calendar_times( $post_id );
	if ( ! $times ) {
		return '';
	}

	$schedule = msrevents_get_event_schedule_fields( $post_id );
	$uid      = 'msr-event-' . $post_id . '@' . wp_parse_url( home_url(), PHP_URL_HOST );
	$summary  = wp_strip_all_tags( get_the_title( $post_id ) );
	$desc     = __( 'Portfolio demonstration — calendar export preview.', 'msrevents' );
	$location = $schedule['venue_name'];

	$lines = array(
		'BEGIN:VCALENDAR',
		'VERSION:2.0',
		'PRODID:-//MSR Events//Hub Demo//EN',
		'CALSCALE:GREGORIAN',
		'METHOD:PUBLISH',
		'BEGIN:VEVENT',
		'UID:' . $uid,
		'DTSTAMP:' . gmdate( 'Ymd\THis\Z' ),
		'DTSTART:' . gmdate( 'Ymd\THis\Z', $times['start'] ),
		'DTEND:' . gmdate( 'Ymd\THis\Z', $times['end'] ),
		'SUMMARY:' . msrevents_ics_escape( $summary ),
		'DESCRIPTION:' . msrevents_ics_escape( $desc ),
	);

	if ( $location ) {
		$lines[] = 'LOCATION:' . msrevents_ics_escape( $location );
	}

	$lines[] = 'END:VEVENT';
	$lines[] = 'END:VCALENDAR';

	return implode( "\r\n", $lines ) . "\r\n";
}

/**
 * Escape text for ICS properties.
 *
 * @param string $text Raw text.
 * @return string
 */
function msrevents_ics_escape( $text ) {
	$text = str_replace( array( '\\', ';', ',', "\n", "\r" ), array( '\\\\', '\;', '\,', '\n', '' ), (string) $text );

	return $text;
}

/**
 * Serve ICS download when requested.
 *
 * @return void
 */
function msrevents_maybe_serve_event_ics() {
	if ( empty( $_GET['msr_event_ics'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return;
	}

	$post_id = (int) $_GET['msr_event_ics']; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( ! $post_id || 'event' !== get_post_type( $post_id ) ) {
		return;
	}

	$ics = msrevents_build_event_ics( $post_id );
	if ( '' === $ics ) {
		status_header( 404 );
		exit;
	}

	$slug = sanitize_file_name( get_post_field( 'post_name', $post_id ) );
	header( 'Content-Type: text/calendar; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename="' . $slug . '-demo.ics"' );
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- plain ICS body
	echo $ics;
	exit;
}
add_action( 'template_redirect', 'msrevents_maybe_serve_event_ics', 0 );

/**
 * Registration countdown band (registration phase only).
 *
 * @param int $post_id Event post ID.
 * @return void
 */
function msrevents_render_event_registration_countdown( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : (int) get_the_ID();
	if ( 'registration' !== msrevents_get_event_lifecycle_phase( $post_id ) ) {
		return;
	}

	$days = msrevents_get_event_days_until_start( $post_id );
	if ( null === $days ) {
		return;
	}
	?>
	<div class="events-registration-countdown msr-reveal" role="status">
		<p class="events-registration-countdown__label mb-1"><?php esc_html_e( 'Registration window', 'msrevents' ); ?></p>
		<p class="events-registration-countdown__value mb-0">
			<?php
			printf(
				/* translators: %d: days until event start */
				esc_html( _n( '%d day until programme start', '%d days until programme start', $days, 'msrevents' ) ),
				(int) $days
			);
			?>
		</p>
		<p class="events-registration-countdown__note small mb-0"><?php esc_html_e( 'Portfolio demo — live ticketing connects at launch.', 'msrevents' ); ?></p>
	</div>
	<?php
}

/**
 * Add to calendar demo band on event singles.
 *
 * @param int $post_id Event post ID.
 * @return void
 */
function msrevents_render_event_calendar_demo( $post_id = 0 ) {
	$post_id    = $post_id ? (int) $post_id : (int) get_the_ID();
	$google_url = msrevents_get_event_google_calendar_url( $post_id );
	$ics_url    = msrevents_get_event_ics_url( $post_id );

	if ( ! $google_url && ! $ics_url ) {
		return;
	}
	?>
	<section class="events-event-calendar msr-reveal" aria-labelledby="events-event-calendar-heading-<?php echo esc_attr( (string) $post_id ); ?>">
		<h2 id="events-event-calendar-heading-<?php echo esc_attr( (string) $post_id ); ?>" class="h6 events-event-calendar__title">
			<?php esc_html_e( 'Add to calendar (demo)', 'msrevents' ); ?>
		</h2>
		<p class="events-event-calendar__lead small mb-2"><?php esc_html_e( 'Export a preview invite — no live reminders are sent from this portfolio site.', 'msrevents' ); ?></p>
		<ul class="events-event-calendar__list list-unstyled d-flex flex-wrap gap-2 mb-0" role="list">
			<?php if ( $google_url ) : ?>
			<li>
				<a class="events-event-calendar__link btn btn-outline-primary btn-sm" href="<?php echo esc_url( $google_url ); ?>" target="_blank" rel="noopener noreferrer">
					<?php esc_html_e( 'Google Calendar', 'msrevents' ); ?>
				</a>
			</li>
			<?php endif; ?>
			<?php if ( $ics_url ) : ?>
			<li>
				<a class="events-event-calendar__link btn btn-outline-primary btn-sm" href="<?php echo esc_url( $ics_url ); ?>" download>
					<?php esc_html_e( 'Download .ics', 'msrevents' ); ?>
				</a>
			</li>
			<?php endif; ?>
		</ul>
	</section>
	<?php
}

/**
 * Whether sticky mobile register CTA should render.
 *
 * @param int $post_id Event post ID.
 * @return bool
 */
function msrevents_should_show_sticky_event_cta( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : (int) get_the_ID();
	if ( 'event' !== get_post_type( $post_id ) ) {
		return false;
	}

	$link = get_field( 'link1', $post_id );

	return is_array( $link ) && ! empty( $link['url'] ) && ! empty( $link['title'] );
}

/**
 * Sticky mobile register CTA on event singles.
 *
 * @return void
 */
function msrevents_render_sticky_event_cta() {
	if ( ! is_singular( 'event' ) || ! msrevents_should_show_sticky_event_cta() ) {
		return;
	}

	$link = get_field( 'link1' );
	if ( ! is_array( $link ) ) {
		return;
	}

	$target = ! empty( $link['target'] ) ? (string) $link['target'] : '_self';
	?>
	<aside class="events-sticky-cta" role="region" aria-label="<?php esc_attr_e( 'Register preview', 'msrevents' ); ?>">
		<div class="events-sticky-cta__inner">
			<a class="btn btn-primary events-sticky-cta__btn" href="<?php echo esc_url( (string) $link['url'] ); ?>" target="<?php echo esc_attr( $target ); ?>">
				<?php echo esc_html( (string) $link['title'] ); ?>
			</a>
			<p class="events-sticky-cta__note mb-0"><?php esc_html_e( 'Preview — ticketing connects at launch', 'msrevents' ); ?></p>
		</div>
	</aside>
	<?php
}
add_action( 'wp_footer', 'msrevents_render_sticky_event_cta', 20 );

/**
 * Body class when sticky CTA is active.
 *
 * @param string[] $classes Body classes.
 * @return string[]
 */
function msrevents_sticky_event_cta_body_class( $classes ) {
	if ( is_singular( 'event' ) && msrevents_should_show_sticky_event_cta() ) {
		$classes[] = 'msrevents-event-single--sticky-cta';
	}

	return $classes;
}
add_filter( 'body_class', 'msrevents_sticky_event_cta_body_class' );
