<?php
/**
 * Template part for displaying a message that posts cannot be found
 *
 * @package msrevents
 */

$context = 'listing';
if ( is_search() ) {
	$context = 'search';
} elseif ( is_category() || is_archive() ) {
	$context = 'archive';
}

msrevents_render_empty_state(
	array(
		'context' => $context,
		'search'  => is_search(),
	)
);

if ( is_search() ) {
	msrevents_render_search_popular_terms();
	msrevents_render_search_empty_featured_events();
}
