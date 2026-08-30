<?php
/**
 * Theme setup — body class and programme shell hooks.
 *
 * @package msrevents
 */

/**
 * Programme body class for scoped SCSS.
 *
 * @param string[] $classes Body classes.
 * @return string[]
 */
function msrevents_body_classes( $classes ) {
	$classes[] = 'msr-events';
	$classes[] = 'msr-assets-self-hosted';
	return $classes;
}
add_filter( 'body_class', 'msrevents_body_classes' );

/**
 * Core theme supports (document title, HTML5).
 */
function msrevents_theme_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
}
add_action( 'after_setup_theme', 'msrevents_theme_setup' );

/**
 * Drop "Category:", "Tag:", etc. if archive titles use get_the_archive_title().
 */
add_filter( 'get_the_archive_title_prefix', '__return_empty_string' );
