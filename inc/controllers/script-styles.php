<?php

/**
 * Register styles and scripts for WP Theme.
 */

 function theme_scripts() {
	wp_register_style('googlefonts', 'https://fonts.googleapis.com/css2?family=DM+Sans&family=Victor+Mono&display=swap', array(), null);
	wp_enqueue_style('basecss', 'https://cdn.jsdelivr.net/npm/purecss@3.0.0/build/base-min.css');
	wp_enqueue_style('animatecss', 'https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css');
	wp_enqueue_style('fancyboxcss', 'https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.css');
	wp_enqueue_style('bootstrapcss', 'https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css');
	wp_enqueue_style('stylecss', get_template_directory_uri() . '/style.css' , array(), time());
	wp_register_style('appcss', get_template_directory_uri() . '/dist/app.css' , array(), time());
	wp_enqueue_style('googlefonts');
	wp_enqueue_style('basecss');
    wp_enqueue_style('appcss');

	wp_register_script( 'fontawesomejs', 'https://kit.fontawesome.com/2c48647809.js' );
	wp_register_script( 'lottiejs', 'https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js' );
	wp_register_script( 'bootstrapjs', 'https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js' );
	wp_enqueue_script('animationjs', get_template_directory_uri() . '/src/js/animation.js', array('jquery'));
	wp_enqueue_script('navjs', get_template_directory_uri() . '/src/js/navigation.js', array(), _S_VERSION, true );
    wp_register_script('appjs', get_template_directory_uri() . '/dist/app.js' , ['jquery'], 1 , true);
	wp_register_script( 'loadmore_script', get_stylesheet_directory_uri() . '/src/js/ajax.js', array('jquery') );
	wp_localize_script( 'loadmore_script', 'loadmore_params', array(
		'ajaxurl' => admin_url('admin-ajax.php'),
	) );

 	wp_enqueue_script( 'loadmore_script' );
	wp_enqueue_script('jquery');
	wp_enqueue_script('fontawesomejs');
	wp_enqueue_script('lottiejs');
    wp_enqueue_script('appjs');
}
add_action( 'wp_enqueue_scripts', 'theme_scripts' );