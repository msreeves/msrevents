        <div class="col-xl-4 col-lg-4">
           <div class="panel">
           <?php 
$link = get_field('link');
    $link_url = $link['url'];
    ?>
    <a class="m-1" href="<?php echo esc_url( $link_url ); ?>" target="_blank"> <?php get_template_part( 'templates/partials/featured-image' ); ?></a>
          </div>
         </div>
        <div class="col-lg-8">
           <div class="panel">
            <div class="my-auto">
             <h2><?php the_title(); ?></h1>	
             <p> <?php the_field('summary'); ?></p>
              <a href="<?php echo esc_url( $link_url ); ?>" target="_blank"><button>Read more</button></a>
               </div>
             </div>
         </div>


