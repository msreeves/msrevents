<?php
/**
 * msrevents functions and definitions
 *
 * @package msrevents
 */

 if ( ! defined( '_S_VERSION' ) ) {
	define( '_S_VERSION', '1.0.0' );
}

require_once get_template_directory() . '/inc/setup.php';
require_once get_template_directory() . '/inc/media.php';
require_once get_template_directory() . '/inc/content.php';
require_once get_template_directory() . '/inc/filter-bar.php';
require_once get_template_directory() . '/inc/ecosystem.php';
require_once get_template_directory() . '/inc/programme.php';
require_once get_template_directory() . '/inc/admin.php';
require_once get_template_directory() . '/inc/acf.php';
require_once get_template_directory() . '/inc/options.php';
require_once get_template_directory() . '/inc/search.php';
require_once get_template_directory() . '/inc/footer.php';
require_once get_template_directory() . '/inc/seo.php';
require_once get_template_directory() . '/inc/portfolio.php';
require_once get_template_directory() . '/inc/perf.php';
require_once get_template_directory() . '/inc/event-ux.php';
require_once get_template_directory() . '/inc/archive.php';
require_once get_template_directory() . '/inc/single.php';
require_once get_template_directory() . '/inc/companion.php';
require_once get_template_directory() . '/inc/about.php';
require_once get_template_directory() . '/inc/routes.php';
require_once get_template_directory() . '/inc/sections.php';
require_once get_template_directory() . '/inc/publications.php';

require_once('inc/controllers/cpt.php');
require_once('inc/controllers/cpt-admin.php');
require_once('inc/controllers/wp-menus.php');
require_once('inc/controllers/script-styles.php');

/**
 * Output ACF / legacy rich text without double-escaping HTML.
 *
 * @param mixed $content HTML or plain text.
 * @return void
 */
function msrevents_render_rich_text( $content ) {
	$content = trim( (string) $content );
	if ( '' === $content ) {
		return;
	}
	echo wp_kses_post( $content );
}

/**
 * Primary CTA link styled as button (no nested button).
 *
 * @param array{url?: string, title?: string, target?: string} $link ACF link array.
 * @param string                                               $class Extra classes.
 * @return void
 */
function msrevents_render_cta_link( $link, $class = 'btn btn-primary' ) {
	if ( ! is_array( $link ) || empty( $link['url'] ) || empty( $link['title'] ) ) {
		return;
	}
	$target = ! empty( $link['target'] ) ? (string) $link['target'] : '_self';
	printf(
		'<a class="%s" href="%s" target="%s">%s</a>',
		esc_attr( $class ),
		esc_url( (string) $link['url'] ),
		esc_attr( $target ),
		esc_html( (string) $link['title'] )
	);
}

/**
 * Normalize ACF image / icon field values (ID, array, serialized, or URL string) to a URL.
 *
 * @param mixed $value Raw ACF value.
 * @return string Image URL or empty string.
 */
function msrevents_acf_image_url( $value ) {
	if ( '' === $value || null === $value ) {
		return '';
	}
	if ( is_numeric( $value ) ) {
		$url = wp_get_attachment_image_url( (int) $value, 'full' );
		return $url ? $url : '';
	}
	if ( is_array( $value ) ) {
		if ( ! empty( $value['url'] ) ) {
			return (string) $value['url'];
		}
		if ( ! empty( $value['ID'] ) ) {
			return msrevents_acf_image_url( $value['ID'] );
		}
		return '';
	}
	if ( is_string( $value ) ) {
		$trim = trim( $value );
		if ( '' === $trim ) {
			return '';
		}
		if ( ctype_digit( $trim ) ) {
			return msrevents_acf_image_url( (int) $trim );
		}
		$maybe = maybe_unserialize( $trim );
		if ( $maybe !== $trim && ( is_array( $maybe ) || is_numeric( $maybe ) ) ) {
			return msrevents_acf_image_url( $maybe );
		}
		return $trim;
	}
	return '';
}

/**
 * Sanitize a Font Awesome icon class string for safe output.
 *
 * @param mixed $value Raw ACF value.
 * @return string Space-separated FA classes or empty string.
 */
function msrevents_sanitize_fa_icon_class( $value ) {
	$value = trim( (string) $value );
	if ( '' === $value ) {
		return '';
	}

	$allowed = array();
	foreach ( preg_split( '/\s+/', $value ) as $part ) {
		if ( preg_match( '/^fa-(solid|regular|brands|sharp|light|thin|duotone)$/', $part )
			|| preg_match( '/^fa-[a-z0-9-]+$/', $part ) ) {
			$allowed[] = $part;
		}
	}

	return implode( ' ', $allowed );
}

/**
 * Resolve an award taxonomy value (WP_Term, ID, or slug) to a term slug.
 *
 * @param mixed $term Term object, ID, or slug string.
 * @return string Term slug or empty string.
 */
function msrevents_award_term_slug( $term ) {
	if ( $term instanceof WP_Term ) {
		return (string) $term->slug;
	}
	if ( is_numeric( $term ) ) {
		$resolved = get_term( (int) $term, 'award' );
		return ( $resolved && ! is_wp_error( $resolved ) ) ? (string) $resolved->slug : '';
	}
	if ( is_string( $term ) ) {
		return trim( $term );
	}
	return '';
}

/**
 * Allowed HTML tags for oembed video iframes.
 *
 * @return array<string, array<string, bool>>
 */
function msrevents_video_embed_allowed_html() {
	return array(
		'iframe' => array(
			'src'             => true,
			'width'           => true,
			'height'          => true,
			'frameborder'     => true,
			'allow'           => true,
			'allowfullscreen' => true,
			'title'           => true,
			'referrerpolicy'  => true,
		),
	);
}

/**
 * Sanitize oembed iframe HTML for front-end output.
 *
 * @param string $html Embed markup.
 * @return string
 */
function msrevents_kses_video_embed( $html ) {
	return wp_kses( (string) $html, msrevents_video_embed_allowed_html() );
}

/**
 * Render an ACF oembed / video field value as safe embed HTML.
 *
 * @param mixed $video Raw field value (iframe HTML, URL, or corrupted oembed string).
 * @return string Embed HTML or empty string.
 */
function msrevents_render_video_embed( $video ) {
	$video = trim( (string) $video );
	if ( '' === $video ) {
		return '';
	}

	if ( false !== stripos( $video, '<iframe' ) ) {
		if ( preg_match( '/src=["\']([^"\']+)["\']/', $video, $src_match ) ) {
			$oembed = wp_oembed_get( $src_match[1] );
			if ( $oembed ) {
				return msrevents_kses_video_embed( $oembed );
			}
		}
		return msrevents_kses_video_embed( $video );
	}

	if ( preg_match( '/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]{11})/', $video, $matches ) ) {
		$url   = 'https://www.youtube.com/watch?v=' . $matches[1];
		$embed = wp_oembed_get( $url );
		if ( $embed ) {
			return msrevents_kses_video_embed( $embed );
		}
	}

	if ( filter_var( $video, FILTER_VALIDATE_URL ) ) {
		$embed = wp_oembed_get( $video );
		if ( $embed ) {
			return msrevents_kses_video_embed( $embed );
		}
	}

	return '';
}

/**
 * Load taxonomy terms for filter-tab templates.
 *
 * @param string $taxonomy Taxonomy slug.
 * @param int    $parent   Parent term ID (category hierarchies); 0 for top-level.
 * @return WP_Term[]
 */
function msrevents_get_filter_terms( $taxonomy, $parent = 0 ) {
	$taxonomy = sanitize_key( $taxonomy );

	if ( 'category' === $taxonomy ) {
		$terms = get_categories(
			array(
				'taxonomy'   => 'category',
				'parent'     => (int) $parent,
				'hide_empty' => true,
			)
		);
	} else {
		$terms = get_terms(
			array(
				'taxonomy'   => $taxonomy,
				'hide_empty' => true,
			)
		);
	}

	if ( is_wp_error( $terms ) || ! is_array( $terms ) ) {
		return array();
	}

	return array_values(
		array_filter(
			$terms,
			static function ( $term ) {
				return $term instanceof WP_Term;
			}
		)
	);
}

/**
 * Build WP_Query arguments for filter-tab listings.
 *
 * @param string $post_type Post type slug.
 * @param string $term_slug Term slug; empty string for "all".
 * @param string $taxonomy  Taxonomy slug.
 * @param array  $base_args Extra query arguments.
 * @return array<string, mixed>
 */
function msrevents_filter_tabs_query_args( $post_type, $term_slug, $taxonomy, $base_args = array() ) {
	$args = array_merge(
		array(
			'post_type'      => sanitize_key( $post_type ),
			'posts_per_page' => -1,
			'post_status'    => 'publish',
		),
		$base_args
	);

	$term_slug = sanitize_title( (string) $term_slug );
	if ( '' !== $term_slug ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => sanitize_key( $taxonomy ),
				'field'    => 'slug',
				'terms'    => $term_slug,
			),
		);
	}

	return $args;
}
/**
 * ACF local JSON — save field group definitions to the theme repo.
 * The acf-json/ directory acts as source of truth; sync via WP Admin → Custom Fields → Sync.
 */
add_filter( 'acf/settings/save_json', function () {
	return get_stylesheet_directory() . '/acf-json';
} );

add_filter( 'acf/settings/load_json', function ( $paths ) {
	$paths[] = get_stylesheet_directory() . '/acf-json';
	return $paths;
} );

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function msrevents_setup() {
	/*
		* Enable support for Post Thumbnails on posts and pages.
		*
		*/
	add_theme_support( 'post-thumbnails' );

	set_post_thumbnail_size( 1200, 9999 );



	/*
		* Switch default core markup for search form, comment form, and comments
		* to output valid HTML5.
		*/
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	// Set up the WordPress core custom background feature.
	add_theme_support(
		'custom-background',
		apply_filters(
			'msrevents_custom_background_args',
			array(
				'default-color' => 'ffffff',
				'default-image' => '',
			)
		)
	);

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

	/**
	 * Add support for core custom logo.
	 *
	 */
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);
}
add_action( 'after_setup_theme', 'msrevents_setup' );

/**
 * Custom Logo for WP Theme.
 */

add_filter( 'get_custom_logo', 'add_custom_logo_url' );
function add_custom_logo_url() {
    $custom_logo_id = get_theme_mod( 'custom_logo' );
    $html = sprintf( '<a href="%1$s" class="navbar-brand" rel="home" itemprop="url">%2$s</a>',
            esc_url( '/' ),
            wp_get_attachment_image( $custom_logo_id, 'full', false, array(
                'class'    => 'custom-logo',
            ) )
        );
    return $html;   
} 

function enable_svg_upload( $upload_mimes ) {
	
    $upload_mimes['svg'] = 'image/svg+xml';

    $upload_mimes['svgz'] = 'image/svg+xml';

    return $upload_mimes;

}

add_filter( 'upload_mimes', 'enable_svg_upload', 10, 1 );

function msrevents_loadmore_ajax_handler() {
	check_ajax_referer( 'msr_loadmore', 'nonce' );

	$listing_type = isset( $_POST['listing_type'] ) ? sanitize_text_field( wp_unslash( $_POST['listing_type'] ) ) : '';
	$page         = isset( $_POST['page'] ) ? absint( $_POST['page'] ) : 1;
	$limit        = isset( $_POST['limit'] ) ? absint( $_POST['limit'] ) : 3;
	$limit        = min( max( 1, $limit ), 24 );

	$args = array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'posts_per_page'      => $limit,
		'paged'               => $page + 1,
		'ignore_sticky_posts' => true,
		'no_found_rows'       => false,
	);

	if ( 'archive' === $listing_type ) {
		$category = isset( $_POST['category'] ) ? sanitize_text_field( wp_unslash( $_POST['category'] ) ) : '';
		if ( '' !== $category ) {
			$args['category_name'] = $category;
		}
	} elseif ( 'latest' === $listing_type ) {
		$term_id = isset( $_POST['term_id'] ) ? absint( $_POST['term_id'] ) : 0;
		if ( $term_id > 0 ) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'category',
					'field'    => 'term_id',
					'terms'    => array( $term_id ),
				),
			);
		}
	}

	$query = new WP_Query( $args );

	if ( $query->have_posts() ) {
		echo '<div class="row msr-card-grid">';
		while ( $query->have_posts() ) {
			$query->the_post();
			ob_start();
			get_template_part( 'template-parts/cards/post-card' );
			echo ob_get_clean();
		}
		echo '</div>';
		wp_reset_postdata();
	}

	wp_die( '', '', 200 );
}
add_action('wp_ajax_loadmore','msrevents_loadmore_ajax_handler');
add_action('wp_ajax_nopriv_loadmore','msrevents_loadmore_ajax_handler');

function post_per_page_control( $query ) {
	if ( ! $query->is_main_query() || is_admin() ) {
		return;
	}
	if ( $query->is_archive() ) {
		$query->set( 'posts_per_page', 18 );
	}
	if ( $query->is_category() ) {
		$query->set( 'orderby', 'date' );
		$query->set( 'order', 'ASC' );
	}
}
add_action( 'pre_get_posts', 'post_per_page_control' );

  function wpse_custom_excerpts($limit) {
    return wp_trim_words(get_the_excerpt(), $limit, '[...]');
}