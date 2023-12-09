<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package msrevents
 */

get_header();
        
?>
<main id="site-content">
<?php if (is_page( 80 )) : ?>
    <?php
      $sections = get_field( 'add_sections' );

    if ( $sections ) :
        foreach ( $sections as $section ) :
            $template = str_replace( '_', '-', $section['acf_fc_layout'] );
            get_template_part( 'inc/components/' . $template, '', $section );
        endforeach;
      endif;
?>
<?php else : ?>
  <section>
   <div class="container">
    <div class="panel">
      <h1> <?php the_title(); ?> </h1>
    <?php the_content(); ?>
</div>
  </div>
</section>
<?php endif; ?>
</main>
<?php
get_footer();