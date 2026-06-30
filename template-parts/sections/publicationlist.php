<?php
/**
 * ACF: Flexible Content > Layouts > Publication list
 *
 * @package msrevents
 */

$heading      = isset( $args['title'] ) ? (string) $args['title'] : '';
$introduction = isset( $args['introduction'] ) ? (string) $args['introduction'] : '';
$limit        = isset( $args['limit'] ) ? (int) $args['limit'] : 6;

if ( function_exists( 'msrevents_render_publications_section' ) ) {
	msrevents_render_publications_section(
		array(
			'title'        => $heading,
			'introduction' => $introduction,
			'limit'        => $limit,
		)
	);
}
