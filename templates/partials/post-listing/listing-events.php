<?php
$date = get_field('date');
$time = get_field('time');
$venue = get_field('venue');
?>

<div class="row g-0 mb-5">
     <div class="col-xl-6 col-lg-6"> 
        <?php the_post_thumbnail();
echo get_post(get_post_thumbnail_id())->post_excerpt; ?>
    </div>
     <div class="col-xl-6 col-lg-6">
        <div class="panel text-center">
        <h1><?php the_title(); ?></h1>	
        <h2><?php echo $venue['name']; ?></h2>
          <i class="fa fa-map-marker fa-2xl" aria-hidden="true"></i>
         <p> <?php echo $venue['address']; ?></p>
         <p><?php if($date['start']) : ?>  <i class="fa-solid fa-calendar"></i> <?php endif; ?> <?php echo $date['start']; ?> <?php if($date['finish']) : ?> - <?php echo $date['finish']; ?><?php endif; ?> </p> 
          <p><?php if($time['start']) : ?>  <i class="fa-solid fa-clock"></i> <?php endif; ?> <?php echo $time['start']; ?> <?php if($time['finish']) : ?> - <?php echo $time['finish']; ?><?php endif; ?> </p>
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
          <div class="col-sm-12">
            <div class="panel">
              <?php the_field('information'); ?>
        </div>
          </div>
</div>
