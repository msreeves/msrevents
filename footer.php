<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package msrevents
 */

?>

 <?php get_template_part( 'templates/partials/leaderboard/footer' ); ?>	

	<footer id="colophon" class="site-footer">
			<?php

$has_footer_menu = has_nav_menu( 'footer' );
$has_social_menu = has_nav_menu( 'social' );


// Only output the container if there are elements to display.
if ( $has_footer_menu || $has_social_menu ) {
	?>	
		  <nav class="navbar navbar-expand-lg navbar-light p-4">
        <div class="navbar-nav container-fluid">
			<?php if ( $has_footer_menu ) { ?>
								<?php
								wp_nav_menu(
									array(
										'container'      => '',
										'depth'          => 1,
										'items_wrap'     => '%3$s',
										'theme_location' => 'footer',
									)
								);
								?>
						<?php } ?>
                <div class="social-media ms-lg-auto">
           <?php 
    $locations = get_registered_nav_menus();
    $menus = wp_get_nav_menus();
    $menu_locations = get_nav_menu_locations();

    $location_id = 'social';

    if (isset($menu_locations[ $location_id ])) 
{ 
  foreach ($menus as $menu) 
  {

       if ($menu->term_id == $menu_locations[ $location_id ]) 
    {
      echo '<h2>'.$menu->name.'</h2>';
      // Get the items for this menu
      $menu_items = wp_get_nav_menu_items($menu);
      echo '<div class="d-flex flex-wrap justify-content-evenly">';
      foreach ( $menu_items as $item )
      {

        $id = get_post_meta( $item->ID, '_menu_item_object_id', true );
        $page = get_page( $id );
        //$link = get_page_link( $id ); ?>
<a href="<?php echo $item->url; ?> " target="_blank"><i class="fa-brands fa-<?php echo $item->title ?> fa-2xl mb-3"></i></a>
  <?php 
      }
      echo '</div';

      break;
    }
  }
    } 
    ?>
                </div>
            </div>
    </nav>
	<?php } ?>
	</footer><!-- #colophon -->
<?php wp_footer(); ?>
</body>
</html>