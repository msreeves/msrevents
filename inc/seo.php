<?php
/**
 * Events hub SEO — meta description, Open Graph, Twitter, JSON-LD.
 *
 * Supersedes legacy tenweb_meta_description on hub routes.
 *
 * @package msrevents
 */

/**
 * Whether copy looks like Latin / lorem placeholder (not programme meta).
 *
 * @param string $text Plain text.
 * @return bool
 */
function msrevents_seo_is_placeholder_copy( $text ) {
	$text = strtolower( trim( wp_strip_all_tags( $text ) ) );
	if ( '' === $text ) {
		return true;
	}

	$patterns = array(
		'lorem ipsum',
		'class aptent taciti',
		'dolor sit amet',
		'ut et neque lacus',
		'in et arcu eu dui',
		'nulla consequat et mas',
	);

	foreach ( $patterns as $pattern ) {
		if ( str_contains( $text, $pattern ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Normalise and trim a meta description string.
 *
 * @param string $description Raw description.
 * @return string
 */
function msrevents_seo_normalize_description( $description ) {
	$description = wp_strip_all_tags( (string) $description );
	$description = strip_shortcodes( $description );
	$description = preg_replace( '/\s+/', ' ', $description );
	$description = trim( (string) $description );

	if ( '' === $description || msrevents_seo_is_placeholder_copy( $description ) ) {
		return '';
	}

	return mb_substr( $description, 0, 300, 'UTF-8' );
}

/**
 * Curated meta descriptions for programme pages (slug-keyed).
 *
 * @return array<string, string>
 */
function msrevents_seo_curated_page_descriptions() {
	return array(
		'partners'     => __( 'Meet sponsors and partners supporting MSR Events programmes and portfolio demonstrations.', 'msrevents' ),
		'for-planners' => __( 'Planning resources and journey overview for MSR Events programme hosts and planners.', 'msrevents' ),
		'about-us'     => __( 'About MSR Events — demonstration multisite hub for awards, seminars, and programme routing.', 'msrevents' ),
		'publications' => __( 'MSR Events publications and editorial resources from the events hub.', 'msrevents' ),
		'podcasts'     => __( 'Listen to MSR Events programme podcasts and editorial audio.', 'msrevents' ),
	);
}

/**
 * Front-end routes that use hub SEO output.
 *
 * @return bool
 */
function msrevents_is_seo_route() {
	if ( is_admin() ) {
		return false;
	}

	if ( is_front_page()
		|| is_home()
		|| is_singular()
		|| is_page()
		|| is_search()
		|| is_category()
		|| is_post_type_archive( 'event' )
	) {
		return true;
	}

	return (bool) apply_filters( 'msrevents_is_seo_route', false );
}

/**
 * @return string
 */
function msrevents_seo_site_name() {
	return get_bloginfo( 'name', 'display' );
}

/**
 * @return string
 */
function msrevents_seo_canonical_url() {
	if ( is_singular() ) {
		return (string) get_permalink();
	}
	if ( is_post_type_archive( 'event' ) ) {
		$link = get_post_type_archive_link( 'event' );
		return $link ? (string) $link : home_url( '/' );
	}
	if ( is_category() ) {
		return (string) get_category_link( get_queried_object_id() );
	}
	if ( is_search() ) {
		return (string) get_search_link();
	}
	return home_url( '/' );
}

/**
 * @return string
 */
function msrevents_seo_title() {
	if ( is_singular() ) {
		$post_title = single_post_title( '', false );
		if ( $post_title !== '' ) {
			return $post_title . ' — ' . msrevents_seo_site_name();
		}
	}
	if ( is_post_type_archive( 'event' ) ) {
		return __( 'Our events', 'msrevents' ) . ' — ' . msrevents_seo_site_name();
	}
	if ( is_category() ) {
		return single_cat_title( '', false ) . ' — ' . msrevents_seo_site_name();
	}
	if ( is_search() ) {
		return sprintf(
			/* translators: %s: search query */
			__( 'Search results for "%s"', 'msrevents' ),
			get_search_query()
		) . ' — ' . msrevents_seo_site_name();
	}
	if ( is_front_page() || is_home() ) {
		$tagline = get_bloginfo( 'description', 'display' );
		if ( $tagline !== '' ) {
			return msrevents_seo_site_name() . ' — ' . $tagline;
		}
		return msrevents_seo_site_name();
	}
	if ( is_page() ) {
		return single_post_title( '', false ) . ' — ' . msrevents_seo_site_name();
	}

	return msrevents_seo_site_name();
}

/**
 * @return string
 */
function msrevents_seo_description() {
	$description = '';

	if ( is_front_page() || is_home() ) {
		$description = msrevents_seo_normalize_description( (string) get_bloginfo( 'description', 'display' ) );
		if ( '' === $description ) {
			$description = msrevents_seo_normalize_description( msrevents_get_seo_home_description() );
		}
		return $description;
	}

	if ( is_singular( 'event' ) ) {
		$post_id = get_the_ID();
		$description = msrevents_seo_normalize_description( get_the_excerpt( $post_id ) );
		if ( '' === $description && function_exists( 'msrevents_get_lifecycle_phase_description' ) && function_exists( 'msrevents_get_event_lifecycle_phase' ) ) {
			$description = msrevents_seo_normalize_description(
				msrevents_get_lifecycle_phase_description( msrevents_get_event_lifecycle_phase() )
			);
		}
		if ( '' !== $description ) {
			return $description;
		}
	}

	if ( is_singular() ) {
		global $post;
		if ( $post instanceof WP_Post ) {
			$description = msrevents_seo_normalize_description( $post->post_excerpt );
			if ( '' === $description ) {
				$trimmed = wp_trim_words( wp_strip_all_tags( (string) $post->post_content ), 40, '…' );
				$description = msrevents_seo_normalize_description( $trimmed );
			}
			if ( '' !== $description ) {
				return $description;
			}
		}
	}

	if ( is_post_type_archive( 'event' ) ) {
		return msrevents_seo_normalize_description( msrevents_get_seo_events_archive_description() );
	}

	if ( is_category() ) {
		$description = msrevents_seo_normalize_description( (string) category_description() );
		if ( '' !== $description ) {
			return $description;
		}
	}

	if ( is_search() ) {
		return msrevents_seo_normalize_description( msrevents_get_seo_search_description() );
	}

	if ( is_page() ) {
		global $post;
		if ( $post instanceof WP_Post ) {
			$description = msrevents_seo_normalize_description( $post->post_excerpt );
			if ( '' === $description ) {
				$curated = msrevents_seo_curated_page_descriptions();
				$slug    = sanitize_title( (string) $post->post_name );
				if ( isset( $curated[ $slug ] ) ) {
					$description = msrevents_seo_normalize_description( $curated[ $slug ] );
				} else {
					$trimmed = wp_trim_words( wp_strip_all_tags( (string) $post->post_content ), 40, '…' );
					$description = msrevents_seo_normalize_description( $trimmed );
				}
			}
			if ( '' !== $description ) {
				return $description;
			}
		}
	}

	return msrevents_seo_normalize_description( (string) get_bloginfo( 'description', 'display' ) );
}

/**
 * @param string $url Attachment URL.
 * @return bool
 */
function msrevents_is_raster_image_url( $url ) {
	if ( '' === trim( (string) $url ) ) {
		return false;
	}

	$path = (string) parse_url( $url, PHP_URL_PATH );

	return ! preg_match( '/\.svg$/i', $path );
}

/**
 * Default social image from programme home hero options.
 *
 * @return string
 */
function msrevents_seo_default_hero_image_url() {
	if ( ! function_exists( 'get_field' ) || ! (bool) get_field( 'hero', 'option' ) ) {
		return '';
	}

	$attachment_id = function_exists( 'msrevents_acf_attachment_id' )
		? msrevents_acf_attachment_id( get_field( 'image', 'option' ) )
		: 0;

	if ( $attachment_id ) {
		$url = wp_get_attachment_image_url( $attachment_id, 'large' );
		return ( $url && msrevents_is_raster_image_url( $url ) ) ? $url : '';
	}

	if ( function_exists( 'msrevents_hero_background_url' ) ) {
		$url = msrevents_hero_background_url( get_field( 'image', 'option' ) );
		return ( $url && msrevents_is_raster_image_url( $url ) ) ? $url : '';
	}

	return '';
}

/**
 * @return int
 */
function msrevents_seo_image_attachment_id() {
	if ( is_singular() && has_post_thumbnail() ) {
		return (int) get_post_thumbnail_id( get_the_ID() );
	}

	$logo_id = (int) get_theme_mod( 'custom_logo' );
	if ( $logo_id ) {
		return $logo_id;
	}

	if ( function_exists( 'get_field' ) && (bool) get_field( 'hero', 'option' ) ) {
		$hero_id = function_exists( 'msrevents_acf_attachment_id' )
			? msrevents_acf_attachment_id( get_field( 'image', 'option' ) )
			: 0;
		if ( $hero_id ) {
			return $hero_id;
		}
	}

	return 0;
}

/**
 * @return array{url: string, alt: string, width: int, height: int}
 */
function msrevents_seo_image_meta() {
	$empty = array(
		'url'    => '',
		'alt'    => '',
		'width'  => 0,
		'height' => 0,
	);

	$attachment_id = msrevents_seo_image_attachment_id();
	if ( $attachment_id ) {
		$url = wp_get_attachment_image_url( $attachment_id, 'large' );
		if ( $url && msrevents_is_raster_image_url( $url ) ) {
			$meta = wp_get_attachment_metadata( $attachment_id );
			$alt  = trim( (string) get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) );
			if ( $alt === '' ) {
				$alt = get_the_title( $attachment_id );
			}
			return array(
				'url'    => $url,
				'alt'    => $alt,
				'width'  => isset( $meta['width'] ) ? (int) $meta['width'] : 0,
				'height' => isset( $meta['height'] ) ? (int) $meta['height'] : 0,
			);
		}
	}

	$default_url = msrevents_seo_default_hero_image_url();
	if ( $default_url !== '' ) {
		return array(
			'url'    => $default_url,
			'alt'    => __( 'MSR Events programme hero', 'msrevents' ),
			'width'  => 0,
			'height' => 0,
		);
	}

	return $empty;
}

/**
 * @return array<string, mixed>|null
 */
function msrevents_seo_schema_image() {
	$meta = msrevents_seo_image_meta();
	if ( $meta['url'] === '' ) {
		return null;
	}

	$image = array(
		'@type' => 'ImageObject',
		'url'   => $meta['url'],
	);
	if ( $meta['alt'] !== '' ) {
		$image['caption'] = $meta['alt'];
	}
	if ( $meta['width'] > 0 ) {
		$image['width'] = $meta['width'];
	}
	if ( $meta['height'] > 0 ) {
		$image['height'] = $meta['height'];
	}

	return $image;
}

/**
 * Schema.org eventAttendanceMode from format slug.
 *
 * @param int $post_id Event post ID.
 * @return string
 */
function msrevents_seo_event_attendance_mode( $post_id ) {
	$slug = function_exists( 'msrevents_get_event_format_slug' )
		? msrevents_get_event_format_slug( $post_id )
		: 'hybrid';

	$modes = array(
		'in-person' => 'https://schema.org/OfflineEventAttendanceMode',
		'virtual'   => 'https://schema.org/OnlineEventAttendanceMode',
		'hybrid'    => 'https://schema.org/MixedEventAttendanceMode',
	);

	return $modes[ $slug ] ?? 'https://schema.org/MixedEventAttendanceMode';
}

/**
 * Location block for Event JSON-LD.
 *
 * @param int $post_id Event post ID.
 * @return array<string, mixed>|null
 */
function msrevents_seo_event_location_schema( $post_id ) {
	$format_slug = function_exists( 'msrevents_get_event_format_slug' )
		? msrevents_get_event_format_slug( $post_id )
		: 'hybrid';

	$schedule = function_exists( 'msrevents_get_event_schedule_fields' )
		? msrevents_get_event_schedule_fields( $post_id )
		: array( 'venue_name' => '' );

	$venue_name = $schedule['venue_name'];
	$venue      = function_exists( 'get_field' ) ? get_field( 'venue', $post_id ) : null;
	$address    = '';
	if ( is_array( $venue ) && ! empty( $venue['address'] ) ) {
		$address = trim( wp_strip_all_tags( (string) $venue['address'] ) );
	}

	if ( 'virtual' === $format_slug ) {
		$virtual_url = get_permalink( $post_id );
		if ( function_exists( 'msrevents_get_acf_link_parts' ) ) {
			$link = msrevents_get_acf_link_parts( get_field( 'link1', $post_id ) );
			if ( ! empty( $link['url'] ) ) {
				$virtual_url = $link['url'];
			}
		}

		return array(
			'@type' => 'VirtualLocation',
			'url'   => $virtual_url,
		);
	}

	if ( '' === $venue_name && '' === $address ) {
		return null;
	}

	$place = array(
		'@type' => 'Place',
	);
	if ( $venue_name !== '' ) {
		$place['name'] = $venue_name;
	}
	if ( $address !== '' ) {
		$place['address'] = $address;
	}

	return $place;
}

/**
 * @return array<string, mixed>|null
 */
function msrevents_seo_primary_schema() {
	if ( is_front_page() || is_home() ) {
		$schema = array(
			'@context'    => 'https://schema.org',
			'@type'       => 'WebSite',
			'name'        => msrevents_seo_site_name(),
			'description' => msrevents_seo_description(),
			'url'         => home_url( '/' ),
		);
		$schema_image = msrevents_seo_schema_image();
		if ( $schema_image ) {
			$schema['image'] = $schema_image;
		}
		return $schema;
	}

	if ( is_singular( 'event' ) ) {
		$post_id = get_the_ID();
		$schema  = array(
			'@context'              => 'https://schema.org',
			'@type'                 => 'Event',
			'name'                  => get_the_title( $post_id ),
			'description'           => msrevents_seo_description(),
			'url'                   => get_permalink( $post_id ),
			'eventStatus'           => 'https://schema.org/EventScheduled',
			'eventAttendanceMode'   => msrevents_seo_event_attendance_mode( $post_id ),
			'organizer'             => array(
				'@type' => 'Organization',
				'name'  => msrevents_seo_site_name(),
				'url'   => home_url( '/' ),
			),
		);

		if ( function_exists( 'msrevents_get_event_calendar_times' ) ) {
			$times = msrevents_get_event_calendar_times( $post_id );
			if ( $times ) {
				$schema['startDate'] = gmdate( 'c', $times['start'] );
				$schema['endDate']   = gmdate( 'c', $times['end'] );
			}
		}

		$location = msrevents_seo_event_location_schema( $post_id );
		if ( $location ) {
			$schema['location'] = $location;
		}

		$schema_image = msrevents_seo_schema_image();
		if ( $schema_image ) {
			$schema['image'] = $schema_image;
		}

		if ( function_exists( 'msrevents_get_acf_link_parts' ) ) {
			$link = msrevents_get_acf_link_parts( get_field( 'link1', $post_id ) );
			if ( ! empty( $link['url'] ) ) {
				$schema['offers'] = array(
					'@type'         => 'Offer',
					'url'           => $link['url'],
					'availability'  => 'https://schema.org/InStock',
					'price'         => '0',
					'priceCurrency' => 'GBP',
					'description'   => __( 'Portfolio demonstration — ticketing connects at launch.', 'msrevents' ),
				);
			}
		}

		return $schema;
	}

	return null;
}

/**
 * @return array<int, array<string, mixed>>
 */
function msrevents_seo_breadcrumb_schema_items() {
	$items    = array();
	$position = 1;

	$items[] = array(
		'@type'    => 'ListItem',
		'position' => $position++,
		'name'     => __( 'Home', 'msrevents' ),
		'item'     => home_url( '/' ),
	);

	if ( is_singular( 'event' ) ) {
		$archive = get_post_type_archive_link( 'event' );
		if ( $archive ) {
			$items[] = array(
				'@type'    => 'ListItem',
				'position' => $position++,
				'name'     => __( 'Our events', 'msrevents' ),
				'item'     => $archive,
			);
		}
		$items[] = array(
			'@type'    => 'ListItem',
			'position' => $position++,
			'name'     => get_the_title(),
			'item'     => get_permalink(),
		);
	}

	return $items;
}

/**
 * @return void
 */
function msrevents_output_seo_tags() {
	if ( ! msrevents_is_seo_route() ) {
		return;
	}

	$title       = msrevents_seo_title();
	$description = msrevents_seo_description();
	$url         = msrevents_seo_canonical_url();
	$image_meta  = msrevents_seo_image_meta();
	$image       = $image_meta['url'];
	$og_type     = is_singular( 'event' ) ? 'website' : ( is_singular() ? 'article' : 'website' );

	echo '<meta name="description" content="' . esc_attr( $description ) . '" />' . "\n";
	echo '<link rel="canonical" href="' . esc_url( $url ) . '" />' . "\n";

	echo '<meta property="og:site_name" content="' . esc_attr( msrevents_seo_site_name() ) . '" />' . "\n";
	echo '<meta property="og:title" content="' . esc_attr( wp_strip_all_tags( $title ) ) . '" />' . "\n";
	echo '<meta property="og:description" content="' . esc_attr( $description ) . '" />' . "\n";
	echo '<meta property="og:url" content="' . esc_url( $url ) . '" />' . "\n";
	echo '<meta property="og:type" content="' . esc_attr( $og_type ) . '" />' . "\n";
	echo '<meta property="og:locale" content="' . esc_attr( str_replace( '_', '-', get_locale() ) ) . '" />' . "\n";

	if ( $image !== '' ) {
		echo '<meta property="og:image" content="' . esc_url( $image ) . '" />' . "\n";
		if ( $image_meta['alt'] !== '' ) {
			echo '<meta property="og:image:alt" content="' . esc_attr( $image_meta['alt'] ) . '" />' . "\n";
		}
		if ( $image_meta['width'] > 0 ) {
			echo '<meta property="og:image:width" content="' . esc_attr( (string) $image_meta['width'] ) . '" />' . "\n";
		}
		if ( $image_meta['height'] > 0 ) {
			echo '<meta property="og:image:height" content="' . esc_attr( (string) $image_meta['height'] ) . '" />' . "\n";
		}
	}

	echo '<meta name="twitter:card" content="' . esc_attr( $image !== '' ? 'summary_large_image' : 'summary' ) . '" />' . "\n";
	echo '<meta name="twitter:title" content="' . esc_attr( wp_strip_all_tags( $title ) ) . '" />' . "\n";
	echo '<meta name="twitter:description" content="' . esc_attr( $description ) . '" />' . "\n";
	if ( $image !== '' ) {
		echo '<meta name="twitter:image" content="' . esc_url( $image ) . '" />' . "\n";
	}

	$schemas = array();
	$primary = msrevents_seo_primary_schema();
	if ( $primary ) {
		$schemas[] = $primary;
	}

	$crumbs = msrevents_seo_breadcrumb_schema_items();
	if ( count( $crumbs ) > 1 ) {
		$schemas[] = array(
			'@context'        => 'https://schema.org',
			'@type'           => 'BreadcrumbList',
			'itemListElement' => $crumbs,
		);
	}

	foreach ( $schemas as $schema ) {
		echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
	}
}
add_action( 'wp_head', 'msrevents_output_seo_tags', 1 );

/**
 * @param string $title Default document title.
 * @return string
 */
function msrevents_filter_document_title( $title ) {
	if ( msrevents_is_seo_route() ) {
		return msrevents_seo_title();
	}
	return $title;
}
add_filter( 'pre_get_document_title', 'msrevents_filter_document_title' );
