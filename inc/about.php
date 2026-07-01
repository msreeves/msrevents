<?php
/**
 * About page content helpers.
 *
 * @package msrevents
 */

/**
 * Programme map cards for the about template.
 *
 * @return array<int, array{title: string, copy: string, url: string, cta: string}>
 */
function msrevents_get_about_programme_cards() {
	$links = function_exists( 'msrevents_get_ecosystem_links' ) ? msrevents_get_ecosystem_links() : array();
	$cards = array();

	foreach ( $links as $link ) {
		$cards[] = array(
			'title' => (string) ( $link['label'] ?? '' ),
			'copy'  => (string) ( $link['description'] ?? '' ),
			'url'   => (string) ( $link['url'] ?? '' ),
			'cta'   => (string) ( $link['cta'] ?? __( 'Learn more', 'msrevents' ) ),
		);
	}

	if ( $cards ) {
		return $cards;
	}

	return array(
		array(
			'title' => __( 'MSR Events hub', 'msrevents' ),
			'copy'  => __( 'Programme router for awards, seminars, stories, and ecosystem links.', 'msrevents' ),
			'url'   => home_url( '/' ),
			'cta'   => __( 'Visit hub home', 'msrevents' ),
		),
	);
}
