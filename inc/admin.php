<?php
/**
 * Admin-first helpers — pages, menus, and taxonomy resolution (not hardcoded IDs in templates).
 *
 * @package msrevents
 */

/**
 * Published page permalink by slug, with optional path fallback.
 *
 * @param string $slug     Page post_name.
 * @param string $fallback Relative path if page missing.
 * @return string
 */
function msrevents_get_page_url( $slug, $fallback = '' ) {
	$page = get_page_by_path( $slug, OBJECT, 'page' );
	if ( $page instanceof WP_Post && 'publish' === $page->post_status ) {
		return (string) get_permalink( $page );
	}
	if ( '' !== $fallback ) {
		return home_url( $fallback );
	}
	return '';
}

/**
 * Programme home page ID from Reading settings or known slugs (not a hardcoded post ID).
 *
 * @return int
 */
function msrevents_get_programme_home_page_id() {
	$front_id = (int) get_option( 'page_on_front' );
	if ( $front_id > 0 ) {
		return $front_id;
	}

	foreach ( array( 'home' ) as $slug ) {
		$page = get_page_by_path( $slug, OBJECT, 'page' );
		if ( $page instanceof WP_Post && 'publish' === $page->post_status ) {
			return (int) $page->ID;
		}
	}

	return 0;
}

/**
 * Whether the current request is the events hub programme home (hero + flexible sections).
 *
 * @return bool
 */
function msrevents_is_programme_home() {
	if ( is_front_page() ) {
		return true;
	}

	$home_id = msrevents_get_programme_home_page_id();
	if ( $home_id > 0 && is_page( $home_id ) ) {
		return true;
	}

	return (bool) apply_filters( 'msrevents_is_programme_home', false );
}

/**
 * Whether the programme home hero should render (default-on unless explicitly disabled).
 *
 * @return bool
 */
function msrevents_should_show_home_hero() {
	if ( ! msrevents_is_programme_home() ) {
		return false;
	}

	$hero = get_field( 'hero', 'option' );
	if ( false === $hero || 0 === $hero || '0' === $hero ) {
		return false;
	}

	return true;
}

/**
 * Sponsored category slug (admin-managed term).
 *
 * @return string
 */
function msrevents_sponsored_category_slug() {
	return (string) apply_filters( 'msrevents_sponsored_category_slug', 'sponsored-content' );
}

/**
 * Category IDs to hide from public term lists and treat as sponsored ribbons.
 *
 * @return int[]
 */
function msrevents_get_excluded_sponsored_category_ids() {
	$ids = array();

	$term = get_term_by( 'slug', msrevents_sponsored_category_slug(), 'category' );
	if ( $term instanceof WP_Term ) {
		$ids[] = (int) $term->term_id;
	}

	$legacy = (int) apply_filters( 'msrevents_sponsored_category_id', 6 );
	if ( $legacy > 0 && ! in_array( $legacy, $ids, true ) ) {
		$ids[] = $legacy;
	}

	return array_values( array_unique( array_filter( $ids ) ) );
}

/**
 * Whether a post is in the sponsored category.
 *
 * @param int|null $post_id Post ID (default current post).
 * @return bool
 */
function msrevents_is_sponsored_post( $post_id = null ) {
	$post_id = null === $post_id ? get_the_ID() : (int) $post_id;
	if ( $post_id <= 0 ) {
		return false;
	}
	foreach ( msrevents_get_excluded_sponsored_category_ids() as $term_id ) {
		if ( has_category( $term_id, $post_id ) ) {
			return true;
		}
	}
	return false;
}

/**
 * Exclude sponsored category terms on the front end (slug-based + optional filter ID).
 *
 * @param WP_Term[]|false $terms    Terms.
 * @param int             $post_id  Post ID.
 * @param string          $taxonomy Taxonomy.
 * @return WP_Term[]|false
 */
function msrevents_filter_sponsored_category_terms( $terms, $post_id, $taxonomy ) {
	unset( $post_id );
	if ( is_admin() || ! is_array( $terms ) || 'category' !== $taxonomy ) {
		return $terms;
	}

	$exclude = msrevents_get_excluded_sponsored_category_ids();
	if ( ! $exclude ) {
		return $terms;
	}

	foreach ( $terms as $key => $term ) {
		if ( $term instanceof WP_Term && in_array( (int) $term->term_id, $exclude, true ) ) {
			unset( $terms[ $key ] );
		}
	}

	return $terms;
}
add_filter( 'get_the_terms', 'msrevents_filter_sponsored_category_terms', 100, 3 );

/**
 * Nav links from a registered theme location (Appearance → Menus).
 *
 * @param string $location Theme location slug.
 * @return array<int, array{title: string, url: string}>
 */
function msrevents_get_nav_links_from_location( $location ) {
	$locations = get_nav_menu_locations();
	if ( empty( $locations[ $location ] ) ) {
		return array();
	}

	$items = wp_get_nav_menu_items( (int) $locations[ $location ] );
	if ( ! $items ) {
		return array();
	}

	$links = array();
	foreach ( $items as $item ) {
		if ( empty( $item->url ) || 'publish' !== $item->post_status ) {
			continue;
		}
		$links[] = array(
			'title' => $item->title,
			'url'   => $item->url,
		);
	}

	return $links;
}

/**
 * Fallback primary IA when no menu is assigned to menu-1.
 *
 * @return array<int, array{title: string, url: string}>
 */
function msrevents_get_primary_nav_fallback_links() {
	$links = array(
		array(
			'title' => __( 'Home', 'msrevents' ),
			'url'   => home_url( '/' ),
		),
		array(
			'title' => __( 'Our events', 'msrevents' ),
			'url'   => msrevents_get_page_url( 'our-events', '/our-events/' ),
		),
		array(
			'title' => __( 'Topics', 'msrevents' ),
			'url'   => msrevents_get_page_url( 'topics', '/topics/' ),
		),
		array(
			'title' => __( 'For planners', 'msrevents' ),
			'url'   => msrevents_get_page_url( 'for-planners', '/for-planners/' ),
		),
		array(
			'title' => __( 'About', 'msrevents' ),
			'url'   => msrevents_get_page_url( 'about-us', '/about-us/' ),
		),
	);

	return array_values(
		array_filter(
			$links,
			static function ( $link ) {
				return ! empty( $link['url'] );
			}
		)
	);
}

/**
 * Fallback primary menu when no menu is assigned to menu-1.
 *
 * @return void
 */
function msrevents_primary_menu_fallback() {
	echo '<div id="cssmenu" class="events-nav"><ul class="events-nav__list">';
	foreach ( msrevents_get_primary_nav_fallback_links() as $link ) {
		if ( empty( $link['url'] ) ) {
			continue;
		}
		printf(
			'<li class="events-nav__item"><a class="events-nav__link" href="%s"><span>%s</span></a></li>',
			esc_url( $link['url'] ),
			esc_html( $link['title'] )
		);
	}
	echo '</ul></div>';
}

/**
 * Whether legacy leaderboard advert carousels should render.
 *
 * Default off unless header adverts exist in admin (enable via filter when needed).
 *
 * @return bool
 */
function msrevents_show_leaderboard_ads() {
	if ( is_admin() ) {
		return false;
	}

	$default = false;

	$header_ads = get_posts(
		array(
			'post_type'      => 'advert',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'tax_query'      => array(
				array(
					'taxonomy' => 'location',
					'field'    => 'slug',
					'terms'    => 'header',
				),
			),
		)
	);

	if ( ! empty( $header_ads ) ) {
		$default = true;
	}

	/**
	 * Filter leaderboard advert partials on hub routes.
	 *
	 * @param bool $show Whether to show header/footer advert carousels.
	 */
	return (bool) apply_filters( 'msrevents_show_leaderboard_ads', $default );
}

/**
 * Footer explore fallback when no footer menu is assigned.
 *
 * @return array<int, array{title: string, url: string}>
 */
function msrevents_get_footer_explore_links() {
	foreach ( array( 'footer', 'menu-1' ) as $location ) {
		$links = msrevents_get_nav_links_from_location( $location );
		if ( ! empty( $links ) ) {
			return $links;
		}
	}

	$links = array(
		array(
			'title' => __( 'Home', 'msrevents' ),
			'url'   => home_url( '/' ),
		),
		array(
			'title' => __( 'Our events', 'msrevents' ),
			'url'   => msrevents_get_page_url( 'our-events', '/our-events/' ),
		),
		array(
			'title' => __( 'Topics', 'msrevents' ),
			'url'   => msrevents_get_page_url( 'topics', '/topics/' ),
		),
		array(
			'title' => __( 'For planners', 'msrevents' ),
			'url'   => msrevents_get_page_url( 'for-planners', '/for-planners/' ),
		),
		array(
			'title' => __( 'About', 'msrevents' ),
			'url'   => msrevents_get_page_url( 'about-us', '/about-us/' ),
		),
		array(
			'title' => __( 'Privacy', 'msrevents' ),
			'url'   => msrevents_get_page_url( 'privacy', '/privacy/' ),
		),
	);

	return array_values(
		array_filter(
			$links,
			static function ( $link ) {
				return ! empty( $link['url'] );
			}
		)
	);
}
