     <?php
/**
 * Template Name: Podcast Template
 *
 * @package WordPress
 * @subpackage msrevents
 * @since msrevents 1.0
 */
get_header();
?>
<section>
  <div class="container">
      <div class="panel">
        <?php the_title( '<h1>', '</h1>' ); ?>
          <?php the_content(); ?>
      </div>
        <?php get_template_part( 'templates/partials/post-listing/listing-podcast' ); ?>
    </div>
    </section>
      <?php
get_footer();