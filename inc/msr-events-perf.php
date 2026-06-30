<?php
/**
 * Front-end performance helpers — LCP preload, image dimensions.
 *
 * @package msrevents
 */

/**
 * Resolve an ACF image field to a valid attachment ID.
 *
 * @param mixed $value Raw ACF value.
 * @return int
 */
function msrevents_acf_attachment_id( $value ) {
	if ( '' === $value || null === $value ) {
		return 0;
	}
	if ( is_numeric( $value ) ) {
		return msrevents_sanitize_attachment_id( (int) $value );
	}
	if ( is_array( $value ) && ! empty( $value['ID'] ) ) {
		return msrevents_sanitize_attachment_id( (int) $value['ID'] );
	}
	if ( is_string( $value ) ) {
		$trim = trim( $value );
		if ( '' === $trim ) {
			return 0;
		}
		if ( ctype_digit( $trim ) ) {
			return msrevents_sanitize_attachment_id( (int) $trim );
		}
		$maybe = maybe_unserialize( $trim );
		if ( $maybe !== $trim ) {
			return msrevents_acf_attachment_id( $maybe );
		}
	}
	return 0;
}

/**
 * Preload programme home hero background (LCP candidate).
 *
 * @return void
 */
function msrevents_preload_programme_home_hero() {
	if ( is_admin() || ! function_exists( 'msrevents_is_programme_home' ) || ! msrevents_is_programme_home() ) {
		return;
	}
	if ( ! function_exists( 'get_field' ) || ! (bool) get_field( 'hero', 'option' ) ) {
		return;
	}

	$attachment_id = msrevents_acf_attachment_id( get_field( 'image', 'option' ) );
	$src           = $attachment_id ? wp_get_attachment_image_url( $attachment_id, 'medium_large' ) : '';
	if ( ! $src ) {
		$src = msrevents_hero_background_url( get_field( 'image', 'option' ) );
	}
	if ( ! $src ) {
		return;
	}

	printf(
		'<link rel="preload" as="image" href="%s" fetchpriority="high" />' . "\n",
		esc_url( $src )
	);
}
add_action( 'wp_head', 'msrevents_preload_programme_home_hero', 2 );

/**
 * Preload display font for programme home (reduces FOIT on hero H1).
 *
 * @return void
 */
function msrevents_preload_programme_home_font() {
	if ( is_admin() || ! function_exists( 'msrevents_is_programme_home' ) || ! msrevents_is_programme_home() ) {
		return;
	}

	$font_path = get_template_directory() . '/dist/playfair-display-latin-700-normal.woff2';
	if ( ! is_readable( $font_path ) ) {
		return;
	}

	$font_url = get_template_directory_uri() . '/dist/playfair-display-latin-700-normal.woff2';
	printf(
		'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin />' . "\n",
		esc_url( $font_url )
	);
}
add_action( 'wp_head', 'msrevents_preload_programme_home_font', 3 );
