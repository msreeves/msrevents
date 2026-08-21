<?php
/**
 * Event Companion demo bridge — hub event single CTAs + ACF helpers.
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
	$url = msrevents_get_companion_demo_url( $post_id );
	?>
	<p class="events-companion-mini-note small mb-0 mt-3">
		<a href="<?php echo esc_url( $url ); ?>"><?php esc_html_e( 'Open companion demo', 'msrevents' ); ?></a>
		<?php esc_html_e( ' for today / on-now schedule alongside this website.', 'msrevents' ); ?>
	</p>
	<?php
}
