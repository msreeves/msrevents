     <?php
/**
 * Template Name: Events Template
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
        <?php 	
      $args = array(
        'post_type' => 'event',
        'posts_per_page' => -1,

      );
      $all_posts = new WP_Query( $args );		
      ?>

      <?php if ( $all_posts->have_posts() ) : ?>      
          <?php while ( $all_posts->have_posts() ) : $all_posts->the_post(); ?>	
              <?php get_template_part( 'templates/partials/post-listing/listing-events' ); ?>
          <?php endwhile; ?>
          <?php wp_reset_query() ?>
      <?php endif; ?>
</div>
</section>
      <?php
get_footer();