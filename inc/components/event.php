<?php
/**
 * ACF: Flexible Content > Layouts > Event
 * 
 * @package WordPress
 */

 $slider = $args['event'];
?>

<div id="Carousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="3000">
  <?php setup_postdata($event); ?>
                  <div class="carousel-inner">
                            <?php $count = 0; ?>
                            <?php foreach ($slider as $slide) : ?>
                                <?php setup_postdata($slide->ID); ?>
                                <div class="carousel-item <?php echo ($count == 0) ? 'active' : ''; ?>">
                                   <div class="row g-0">
                                    <?php $image = wp_get_attachment_image_src( get_post_thumbnail_id( $slide->ID ), 'single-post-thumbnail' ); ?>
    <div class="p-5 background-image" style="background-image: url('<?php echo $image[0]; ?>');">
  <div class="mask p-5" style="background-color: rgba(0, 0, 0, 0.2);">
    <div class="d-flex justify-content-center align-items-center h-100">
      <div class="text-white text-center">
        <h1><?php echo get_the_title($slide->ID); ?></h1>
         <?php $venue = get_field('venue', $slide->ID); ?>
        <h2><?php echo $venue['name']; ?></h2>
           <i class="fa fa-map-marker fa-2xl" aria-hidden="true"></i>
          <h3><?php echo $venue['address']; ?> </h3> 
          <?php $date = get_field('date', $slide->ID); ?>
         <h3><?php if($date['start']) : ?>  <i class="fa-solid fa-calendar"></i> <?php endif; ?> <?php echo $date['start']; ?> <?php if($date['finish']) : ?> - <?php echo $date['finish']; ?><?php endif; ?> </h3> 
            <?php $time = get_field('time' , $slide->ID); ?>
          <h3><?php if($time['start']) : ?>  <i class="fa-solid fa-clock"></i> <?php endif; ?> <?php echo $time['start']; ?> <?php if($time['finish']) : ?> - <?php echo $time['finish']; ?><?php endif; ?> </h3> 
         <div class="d-flex justify-content-around">
                                        <?php 
$link = get_field('link1', $slide->ID);
if( $link ): 
    $link_url = $link['url'];
    $link_title = $link['title'];
    $link_target = $link['target'] ? $link['target'] : '_self';
    ?>
    <a href="<?php echo esc_url( $link_url ); ?>" target="<?php echo esc_attr( $link_target ); ?>"><button><?php echo esc_html( $link_title ); ?></button></a>
      <?php endif; ?>
                                        <?php 
$link = get_field('link2', $slide->ID);
if( $link ): 
    $link_url = $link['url'];
    $link_title = $link['title'];
    $link_target = $link['target'] ? $link['target'] : '_self';
    ?>
    <a href="<?php echo esc_url( $link_url ); ?>" target="<?php echo esc_attr( $link_target ); ?>"><button class="secondary"><?php echo esc_html( $link_title ); ?></button></a>
      <?php endif; ?>
      </div>
          </div>  
    </div>
</div>
</div>
                        </div>

                                </div>
                                <?php $count++; ?>
                            <?php endforeach; ?>
                        </div>
                            </div>
    <?php 
    // Reset the global post object so that the rest of the page works correctly.
    wp_reset_postdata(); ?>
