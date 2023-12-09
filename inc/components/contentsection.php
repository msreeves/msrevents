<?php
/**
 * ACF: Flexible Content > Layouts > Content Section
 * 
 * @package WordPress
 * @subpackage QORP
 */

$sections = $args['content'];
?>

<section>
   <div class="container">
      <?php foreach( $sections as $section ):   
         $video = $section['video'];
         $heading = $section['title'];
         $image = $section['image'];
         $introduction = $section['introduction'];
         $layout = $section['layout'];
         ?>
        <?php if ($layout == 'fullwidth') { ?>
      <div class="row g-0">
         <div class="col-sm-12">
            <div class="panel">
                  <h1 class="animate__animated animate__backInLeft"><?php echo $heading; ?></h1>
                  <p class="animate__animated animate__backInLeft"> <?php echo $introduction; ?></p>
                    <?php if ( $video ) : ?>
                  <div class="listing-video">
                     <?php echo $video; ?>
                  </div>
                  <?php elseif ( $image ) : ?>
                  <div class="listing-image">
                     <img src="<?php echo $image; ?>"/>
                  </div>
                  <?php endif; ?>
            </div>
         </div>
      </div>
           <?php } elseif ($layout == 'videoimagetext') { ?>     
      <div class="row g-0 mb-5">
         <div class="col-md-6">
            <div class="panel">
               <div class="my-auto">
                  <?php if ( $video ) : ?>
                  <div class="listing-video">
                     <?php echo $video; ?>
                  </div>
                  <?php elseif ( $image ) : ?>
                  <div class="listing-image">
                     <img src="<?php echo $image; ?>"/>
                  </div>
                  <?php endif; ?>
               </div>
            </div>
         </div>
         <div class="col-md-6">
            <div class="panel">
               <div class="my-auto text-center">
                  <h1 class="animate__animated animate__backInLeft"><?php echo $heading; ?></h1>
                  <p class="animate__animated animate__backInLeft"> <?php echo $introduction; ?></p>
               </div>
            </div>
         </div>
      </div>
      <?php } elseif ($layout == 'textimagevideo') { ?>
      <div class="row g-0">
         <div class="col-md-6">
            <div class="panel">
               <div class="my-auto text-center">
                  <h1 class="animate__animated animate__backInLeft"><?php echo $heading; ?></h1>
                  <p class="animate__animated animate__backInLeft"> <?php echo $introduction; ?></p>
               </div>
            </div>
         </div>
         <div class="col-md-6">
            <div class="panel">
               <div class="my-auto">
                  <?php if ( $video ) : ?>
                  <div class="listing-video">
                     <?php echo $video; ?>
                  </div>
                  <?php elseif ( $image ) : ?>
                  <div class="listing-image">
                     <img src="<?php echo $image; ?>"/>
                  </div>
                  <?php endif; ?>
               </div>
            </div>
         </div>
      </div>
   <?php } ?>
      <?php endforeach; ?>
   </div>
</section>