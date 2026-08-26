<?php
/**
 * Event Companion demo bridge — hub event single CTAs, archive/featured picker entry (C1d), ACF helpers.
 *
 * @package msrevents
 */

/**
 * @return string
 */
function msrevents_get_companion_demo_url_default() {
	return 'http://127.0.0.1:8888/sites/portfolio/projects/event-companion/?event=msrseminars';
}

/**
 * @param int $post_id Event post ID.
 * @return string
 */
function msrevents_get_companion_demo_url( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : (int) get_the_ID();
	$url     = '';
	if ( function_exists( 'get_field' ) ) {
		$url = trim( (string) get_field( 'companion_demo_url', $post_id ) );
	}
	if ( '' === $url ) {
		$url = trim( (string) get_post_meta( $post_id, 'companion_demo_url', true ) );
	}
	return $url ? esc_url_raw( $url ) : '';
}

/**
 * @param int $post_id Event post ID.
 * @return string
 */
function msrevents_get_companion_booking_url( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : (int) get_the_ID();
	$url     = '';
	if ( function_exists( 'get_field' ) ) {
		$url = trim( (string) get_field( 'booking_url', $post_id ) );
	}
	if ( '' === $url ) {
		$url = trim( (string) get_post_meta( $post_id, 'booking_url', true ) );
	}
	return $url;
}

/**
 * Hide companion promo on recap (hub equivalent of replay).
 *
 * @param int $post_id Event post ID.
 * @return bool
 */
function msrevents_should_show_companion_cta( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : (int) get_the_ID();
	$phase   = function_exists( 'msrevents_get_event_lifecycle_phase' )
		? msrevents_get_event_lifecycle_phase( $post_id )
		: '';
	if ( 'recap' === $phase ) {
		return false;
	}
	return '' !== msrevents_get_companion_demo_url( $post_id );
}

/**
 * Secondary companion CTA beside primary link1/link2 row.
 *
 * @param int $post_id Event post ID.
 * @return void
 */
function msrevents_render_companion_cta_button( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : (int) get_the_ID();
	if ( ! msrevents_should_show_companion_cta( $post_id ) ) {
		return;
	}
	$url = msrevents_get_companion_demo_url( $post_id );
	?>
	<a class="btn btn-outline-primary events-companion-cta" href="<?php echo esc_url( $url ); ?>">
		<?php esc_html_e( 'Open companion demo', 'msrevents' ); ?>
	</a>
	<?php
}

/**
 * Mini-agenda context line + companion link (S13).
 *
 * @param int $post_id Event post ID.
 * @return void
 */
function msrevents_render_companion_mini_agenda_note( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : (int) get_the_ID();
	if ( ! msrevents_should_show_companion_cta( $post_id ) ) {
		return;
	}
	$url  = msrevents_get_companion_demo_url( $post_id );
	$slug = get_post_field( 'post_name', $post_id );
	?>
	<p class="events-companion-mini-note small mb-0 mt-3">
		<a href="<?php echo esc_url( $url ); ?>"><?php esc_html_e( 'Open companion demo', 'msrevents' ); ?></a>
		<?php
		if ( 'msrawards' === $slug ) {
			esc_html_e( ' for the Awards programme companion alongside this website.', 'msrevents' );
		} else {
			esc_html_e( ' for today / on-now schedule alongside this website.', 'msrevents' );
		}
		?>
	</p>
	<?php
}


/**
 * Local companion SPA root (programme picker — C1a / C1d).
 *
 * @return string
 */
function msrevents_get_companion_picker_url_default() {
	return 'http://127.0.0.1:8888/sites/portfolio/projects/event-companion/';
}

/**
 * One SPA entry for all programmes (no ?event= — lands on picker).
 *
 * @return string
 */
function msrevents_get_companion_picker_url() {
	$stored = trim( (string) get_option( 'msr_events_companion_picker_url', '' ) );
	if ( '' !== $stored ) {
		return esc_url_raw( $stored );
	}
	return msrevents_get_companion_picker_url_default();
}

/**
 * @return bool
 */
function msrevents_should_show_companion_picker_entry() {
	return '' !== msrevents_get_companion_picker_url();
}

/**
 * Archive / featured promo band — one SPA URL for all hub programmes.
 *
 * @return void
 */
function msrevents_render_companion_archive_band() {
	if ( ! msrevents_should_show_companion_picker_entry() ) {
		return;
	}
	$url = msrevents_get_companion_picker_url();
	?>
	<section class="events-companion-band msr-reveal" aria-labelledby="events-companion-band-heading">
		<div class="events-companion-band__inner">
			<div class="events-companion-band__copy">
				<p class="events-companion-band__eyebrow mb-1">
					<?php esc_html_e( 'Companion demo', 'msrevents' ); ?>
				</p>
				<h2 id="events-companion-band-heading" class="h5 events-companion-band__title mb-2">
					<?php esc_html_e( 'All programmes — companion app demo', 'msrevents' ); ?>
				</h2>
				<p class="events-companion-band__lead mb-0">
					<?php esc_html_e( 'One companion for Awards and Seminars. Pick a programme in the demo, then return here anytime. Portfolio demonstration — not an App Store product.', 'msrevents' ); ?>
				</p>
			</div>
			<div class="events-companion-band__actions events-ctas">
				<a class="btn btn-primary events-companion-picker-cta" href="<?php echo esc_url( $url ); ?>">
					<?php esc_html_e( 'Open companion demo', 'msrevents' ); ?>
				</a>
			</div>
		</div>
	</section>
	<?php
}

/**
 * Compact companion link for featured-events CTA row (home).
 *
 * @return void
 */
function msrevents_render_companion_featured_link() {
	if ( ! msrevents_should_show_companion_picker_entry() ) {
		return;
	}
	$url = msrevents_get_companion_picker_url();
	?>
	<a class="btn btn-outline-primary events-companion-featured-cta" href="<?php echo esc_url( $url ); ?>">
		<?php esc_html_e( 'Companion demo', 'msrevents' ); ?>
	</a>
	<?php
}
