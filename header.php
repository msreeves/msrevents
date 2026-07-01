<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="profile" href="https://gmpg.org/xfn/11">
	        <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
	<a class="msr-skip-link" href="#site-content"><?php esc_html_e( 'Skip to content', 'msrevents' ); ?></a>
	<script>document.documentElement.classList.add('js-reveal');</script>
	<noscript><style>.msr-reveal{opacity:1!important;transform:none!important;transition:none!important}</style></noscript>
	<?php
	if ( function_exists( 'msrevents_show_leaderboard_ads' ) && msrevents_show_leaderboard_ads() ) {
		get_template_part( 'templates/partials/leaderboard/header' );
	}
	?>
	<header id="masthead" class="site-header">
    <nav class="navbar navbar-expand-lg navbar-dark" aria-label="<?php esc_attr_e( 'Primary', 'msrevents' ); ?>">
        <div class="container-fluid events-navbar__inner d-flex align-items-center flex-wrap">
              <a class="navbar-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
                <?php
			$custom_logo_id  = (int) get_theme_mod( 'custom_logo' );
			if ( $custom_logo_id ) {
				echo wp_get_attachment_image(
					$custom_logo_id,
					'full',
					false,
					array(
						'class'    => 'custom-logo',
						'alt'      => get_bloginfo( 'name' ),
						'loading'  => 'eager',
						'decoding' => 'async',
					)
				);
			}
			?></a>
             <button class="navbar-toggler collapsed ms-auto d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#site-header-mobile-nav"
          aria-controls="site-header-mobile-nav" aria-expanded="false" aria-label="<?php esc_attr_e( 'Toggle navigation', 'msrevents' ); ?>">
          <span class="icon-bar top-bar"></span>
          <span class="icon-bar middle-bar"></span>
          <span class="icon-bar bottom-bar"></span>
        </button>
        <div class="offcanvas offcanvas-end site-header__mobile-nav ms-lg-auto" tabindex="-1" id="site-header-mobile-nav" aria-labelledby="site-header-mobile-nav-label">
			<div class="offcanvas-header d-lg-none">
				<p class="offcanvas-title h6 mb-0" id="site-header-mobile-nav-label"><?php esc_html_e( 'Menu', 'msrevents' ); ?></p>
				<button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="<?php esc_attr_e( 'Close', 'msrevents' ); ?>"></button>
			</div>
			<div class="offcanvas-body">
				<div class="d-flex flex-column flex-lg-row align-items-stretch align-items-lg-center gap-2 ms-lg-auto w-100">
					<div class="navbar-nav flex-grow-1 flex-lg-grow-0">
						<?php
						wp_nav_menu(
							array(
								'theme_location' => 'menu-1',
								'menu_id'        => 'primary-menu',
								'menu_class'     => 'events-nav__list menu',
								'container'      => 'div',
								'container_id'   => 'cssmenu',
								'container_class'=> 'events-nav menu-header-container',
								'walker'         => new CSS_Menu_Walker(),
								'fallback_cb'    => 'msrevents_primary_menu_fallback',
							)
						);
						?>
					</div>
					<?php if ( function_exists( 'msrevents_render_header_cta' ) ) : ?>
						<?php msrevents_render_header_cta(); ?>
					<?php elseif ( function_exists( 'msr_render_primary_cta' ) ) : ?>
						<?php msr_render_primary_cta(); ?>
					<?php endif; ?>
				</div>
				<div class="events-header-search events-header-search--offcanvas d-lg-none mt-3 pt-3">
					<?php
					get_template_part(
						'template-parts/forms/site-search',
						null,
						array(
							'input_id' => 'msr-events-header-search',
							'compact'  => true,
						)
					);
					?>
				</div>
			</div>
        </div>
        </div>
    </nav>
	</header>

