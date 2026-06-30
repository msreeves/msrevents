<?php
/**
 * Register styles and scripts for WP Theme.
 *
 * Bootstrap CSS, Fancybox, Font Awesome, and theme fonts are bundled via Vite (dist/app.css / dist/app.js).
 * No third-party CDN enqueues on the front end (Phase 19 stack hygiene).
 */

/**
 * Theme asset version from dist filemtime.
 *
 * @param string $relative Path under theme root.
 * @return int|null
 */
function msrevents_asset_version( $relative ) {
	$path = get_template_directory() . '/' . ltrim( $relative, '/' );
	$mtime = @filemtime( $path );
	return $mtime ? (int) $mtime : null;
}

function theme_scripts() {
	$app_css_ver = msrevents_asset_version( 'dist/app.css' );
	$app_js_ver  = msrevents_asset_version( 'dist/app.js' );

	wp_enqueue_style(
		'appcss',
		get_template_directory_uri() . '/dist/app.css',
		array(),
		$app_css_ver
	);

	wp_enqueue_script(
		'appjs',
		get_template_directory_uri() . '/dist/app.js',
		array(),
		$app_js_ver,
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);

	wp_localize_script(
		'appjs',
		'loadmore_params',
		array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'msr_loadmore' ),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'theme_scripts' );

/**
 * Vite bundles are ES modules (dynamic import + import.meta).
 *
 * @param string $tag    Script tag HTML.
 * @param string $handle Script handle.
 * @param string $src    Script URL.
 * @return string
 */
function msrevents_script_loader_tag( $tag, $handle, $src ) {
	unset( $src );
	if ( 'appjs' === $handle && false === strpos( $tag, 'type=' ) ) {
		return str_replace( '<script ', '<script type="module" ', $tag );
	}
	return $tag;
}
add_filter( 'script_loader_tag', 'msrevents_script_loader_tag', 10, 3 );
