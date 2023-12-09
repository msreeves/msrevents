<?php
/**
 * ACF: Flexible Content > Layouts > Publication list
 *
 * @package WordPress
 * @subpackage QORP
 */

$heading = $args["title"];
$introduction = $args["introduction"];
?>

        <section>
          <div class="container">
         <div class="panel">
          <h1 class="animate__animated animate__backInLeft"><?php echo $heading;?></h1>
         <h3 class="animate__animated animate__backInLeft"><?php echo $introduction;?></h3>
            </div>
       <?php
       $args = [
           "post_type" => 'publication',
           "posts_per_page" => -1,
       ];
       $all_posts = new WP_Query($args);
       ?>

      <?php if ($all_posts->have_posts()): ?>
            <div class="row g-0">
          <?php while ($all_posts->have_posts()):
              $all_posts->the_post(); ?>	                
                 <div class="carousel">
			                <div class="carousel-row">       
                          <?php get_template_part(
                              "templates/partials/post-listing/listing-publication"
                          ); ?> 
          <?php
          endwhile; ?>
          <?php wp_reset_query(); ?>
      </div>
       </div>
                          </div>
      <?php endif; ?>
       </div>
  </section>
  