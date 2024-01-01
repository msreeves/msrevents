<?php
/**
 * msrevents functions and definitions
 *
 * @package msrevents
 */

 if ( ! defined( '_S_VERSION' ) ) {
	define( '_S_VERSION', '1.0.0' );
}

require_once('inc/controllers/cpt.php');
require_once('inc/controllers/cpt-admin.php');
require_once('inc/controllers/search.php');
require_once('inc/controllers/wp-menus.php');
require_once('inc/controllers/script-styles.php');

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

function bsubash_loadmore_ajax_handler(){
	$type = $_POST['type'];
	$category = isset($_POST['category']) ? $_POST['category']: '';
	$args['paged'] = $_POST['page'] + 1;
	$args['post_status'] = 'publish';
	$args['posts_per_page'] =  $_POST['limit'];
	if($type == 'archive'){
		$args['category_name'] = $category;
	}
	query_posts( $args );
	if( have_posts() ) :
        echo '<div class="row">';
		while(have_posts()): the_post();	
		echo get_template_part( 'templates/partials/post-listing/posts/maincategory' );
    endwhile;
		echo '</div>';
	endif;
	die;
}
add_action('wp_ajax_loadmore','bsubash_loadmore_ajax_handler');
add_action('wp_ajax_nopriv_loadmore','bsubash_loadmore_ajax_handler');

if ( ! function_exists( 'tenweb_meta_description' ) ) {
    function tenweb_meta_description() { 
        global $post; 
 
        if ( is_singular() ) 
        { 
            $des_post = strip_tags( $post->post_content ); 
            $des_post = strip_shortcodes( $des_post ); 
            $des_post = str_replace( array("\n", "\r", "\t"), ' ', $des_post ); 
            $des_post = mb_substr( $des_post, 0, 300, 'utf8' ); 
            echo '<meta name="description" content="' . $des_post . '" />'. "\n"; 
        } 
 
        if ( is_home() ) 
        { 
            echo '<meta name="description" content="' . get_bloginfo( "description" ) . '" />' . "\n"; 
        } 
 
        if ( is_category() ) {
            $des_cat = strip_tags(category_description());
            echo '<meta name="description" content="' . $des_cat . '" />'. "\n";
        } 
    } 
}
add_action( 'wp_head', 'tenweb_meta_description');

function post_per_page_control( $query ) {
     if ( is_archive() ) {
          $query->set( 'posts_per_page', 18 );
          return;
     }
  }
  add_action( 'pre_get_posts', 'post_per_page_control' );

  function wpse_custom_excerpts($limit) {
    return wp_trim_words(get_the_excerpt(), $limit, '[...]');
}

add_filter('get_the_terms', function ($terms, $post_id, $taxonomy) {
    $exclude_categories = array(6);
    if (!is_admin()) {
        foreach($terms as $key => $term){
            if($term->taxonomy == "category" && in_array($term->term_id, $exclude_categories)) {
                unset($terms[$key]);
            }
        }
    }
    return $terms;
}, 100, 3);