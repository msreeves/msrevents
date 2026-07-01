<?php
/**
 * Flexible home section hydration (legacy flat meta + structured rows).
 *
 * @package msrevents
 */

/**
 * Merge flat ACF meta into a flexible-content row when sub-fields are missing.
 *
 * @param array<string, mixed> $section Section row from add_sections.
 * @param int                  $index   Zero-based section index.
 * @param int                  $post_id Post ID; defaults to current post.
 * @return array<string, mixed>
 */
function msrevents_hydrate_flexible_section( $section, $index, $post_id = 0 ) {
	if ( ! is_array( $section ) || empty( $section['acf_fc_layout'] ) ) {
		return is_array( $section ) ? $section : array();
	}

	$post_id = $post_id ? (int) $post_id : (int) get_the_ID();
	$prefix  = 'add_sections_' . (int) $index . '_';
	$layout  = (string) $section['acf_fc_layout'];

	foreach ( array( 'title', 'introduction', 'name', 'type' ) as $key ) {
		if ( ! empty( $section[ $key ] ) ) {
			continue;
		}
		$value = get_field( $prefix . $key, $post_id );
		if ( $value ) {
			$section[ $key ] = $value;
		}
	}

	if ( 'publicationlist' === $layout ) {
		if ( empty( $section['limit'] ) ) {
			$limit = get_field( $prefix . 'limit', $post_id );
			if ( $limit ) {
				$section['limit'] = (int) $limit;
			}
		}
	}

	return $section;
}
