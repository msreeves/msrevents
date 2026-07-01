<?php
/**
 * MSR Events hub ACF options — admin-first site copy and programme URLs.
 *
 * @package msrevents
 */

/**
 * @param string $field ACF field name.
 * @param string $default Fallback when empty.
 * @return string
 */
function msrevents_get_option_string( $field, $default = '' ) {
	if ( ! function_exists( 'get_field' ) ) {
		return $default;
	}
	$value = get_field( $field, 'option' );
	if ( ! is_string( $value ) || '' === trim( $value ) ) {
		return $default;
	}
	return trim( $value );
}

/**
 * @param string $field ACF field name.
 * @param bool   $default Fallback.
 * @return bool
 */
function msrevents_get_option_bool( $field, $default = false ) {
	if ( ! function_exists( 'get_field' ) ) {
		return $default;
	}
	$value = get_field( $field, 'option' );
	if ( null === $value || '' === $value ) {
		return $default;
	}
	return (bool) $value;
}

/**
 * Ecosystem band heading.
 *
 * @return string
 */
function msrevents_get_ecosystem_band_title() {
	return msrevents_get_option_string(
		'ecosystem_band_title',
		__( 'MSR ecosystem', 'msrevents' )
	);
}

/**
 * Ecosystem band lead copy.
 *
 * @return string
 */
function msrevents_get_ecosystem_band_lead() {
	return msrevents_get_option_string(
		'ecosystem_band_lead',
		__( 'The events hub routes visitors to Awards, Seminars, and Atlas Briefing in the local demonstration estate.', 'msrevents' )
	);
}

/**
 * Our Events archive page intro (below H1).
 *
 * @return string
 */
function msrevents_get_events_archive_intro() {
	return msrevents_get_option_string(
		'events_archive_intro',
		__( 'Browse programme listings on the hub, then follow links to MSR Awards or MSR Seminars for full detail.', 'msrevents' )
	);
}

/**
 * Publications page fallback lead when page body is empty.
 *
 * @return string
 */
function msrevents_get_publications_page_lead() {
	return msrevents_get_option_string(
		'publications_page_lead',
		__( 'Programme guides, delegate handbooks, and sponsor resources for the MSR Events portfolio demonstration.', 'msrevents' )
	);
}

/**
 * About page lead when excerpt is empty.
 *
 * @return string
 */
function msrevents_get_about_lead() {
	return msrevents_get_option_string(
		'about_page_lead',
		__( 'How the MSR Events hub routes visitors to awards, seminars, and Atlas Briefing — built for portfolio demonstration of a modern events platform.', 'msrevents' )
	);
}

/**
 * About programme map intro line.
 *
 * @return string
 */
function msrevents_get_about_programmes_intro() {
	return msrevents_get_option_string(
		'about_programmes_intro',
		__( 'How the hub connects awards, seminars, and publishing surfaces.', 'msrevents' )
	);
}

/**
 * About demonstration disclaimer.
 *
 * @return string
 */
function msrevents_get_about_disclaimer() {
	return msrevents_get_option_string(
		'about_disclaimer',
		__( 'MSR Events is a demonstration hub for portfolio review. Programme copy, registration flows, and partner logos are illustrative — connect production ticketing, legal review, and analytics before launch.', 'msrevents' )
	);
}

/**
 * Footer demo disclaimer line.
 *
 * @return string
 */
function msrevents_get_footer_demo_note() {
	return msrevents_get_option_string(
		'footer_demo_note',
		__( 'Demonstration events hub for portfolio review.', 'msrevents' )
	);
}

/**
 * Whether the footer demo disclaimer is shown.
 *
 * @return bool
 */
function msrevents_show_footer_demo_note() {
	return msrevents_get_option_bool( 'show_footer_demo_note', true );
}

/**
 * Home meta description fallback.
 *
 * @return string
 */
function msrevents_get_seo_home_description() {
	return msrevents_get_option_string(
		'seo_home_description',
		__( 'MSR Events hub — programmes, awards, and seminars for portfolio demonstration. Demonstration property for portfolio purposes.', 'msrevents' )
	);
}

/**
 * Events archive meta description fallback.
 *
 * @return string
 */
function msrevents_get_seo_events_archive_description() {
	return msrevents_get_option_string(
		'seo_events_archive_description',
		__( 'Browse MSR Events programme listings — awards evenings, seminars, and hybrid showcases for portfolio review.', 'msrevents' )
	);
}

/**
 * Search meta description fallback.
 *
 * @return string
 */
function msrevents_get_seo_search_description() {
	return msrevents_get_option_string(
		'seo_search_description',
		__( 'Search MSR Events programmes, topics, and news.', 'msrevents' )
	);
}

/**
 * Programme outbound URL from options (ACF) with legacy wp_option fallback.
 *
 * @param string $slug awards|seminars|publishing.
 * @return string
 */
function msrevents_get_programme_url_option( $slug ) {
	$acf_fields = array(
		'awards'     => 'msr_programme_awards_url',
		'seminars'   => 'msr_programme_seminars_url',
		'publishing' => 'msr_programme_publishing_url',
	);
	$legacy_keys = function_exists( 'msrevents_get_ecosystem_option_keys' )
		? msrevents_get_ecosystem_option_keys()
		: array();

	if ( isset( $acf_fields[ $slug ] ) ) {
		$url = msrevents_get_option_string( $acf_fields[ $slug ], '' );
		if ( '' !== $url ) {
			return esc_url_raw( $url );
		}
	}

	if ( isset( $legacy_keys[ $slug ] ) ) {
		$stored = (string) get_option( $legacy_keys[ $slug ], '' );
		if ( '' !== trim( $stored ) ) {
			return esc_url_raw( $stored );
		}
	}

	return '';
}
