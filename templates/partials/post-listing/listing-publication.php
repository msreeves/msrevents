        <div class="col-lg-4 mx-auto">
           <div class="panel">
           <?php 
$link = get_field('link');
    $link_url = $link['url'];
    $link_title = $link['title'];
    $link_target = $link['target'] ? $link['target'] : '_self';
    ?>
    <a class="m-1" href="<?php echo esc_url( $link_url ); ?>" target="<?php echo esc_attr( $link_target ); ?>"> <?php get_template_part( 'templates/partials/featured-image' ); ?></a>
          </div>
         </div>