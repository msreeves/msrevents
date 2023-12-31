<?php
/**
 * ACF: Flexible Content > Layouts > Call to Action
 *
 * @package WordPress
 */

$heading = $args['title'];
$image = $args['image'];
$introduction = $args['introduction'];
?>


<section class="cta">
        <div class="container">      
            <div class="panel">
            <div class="row">
       		<div class="col-xl-6 col-lg-6">
             <div class="listing-image">
           <img src="<?php echo $image;?>" />
          </div>
                </div>
                <div class="col-xl-6 col-lg-6">
                   <div class="panel">
          <div class="my-auto text-center">
        <h1><?php echo $heading; ?></h1>    
				<p><?php echo $introduction; ?></p>
<div class="ctas">
                      <?php 
$link = $args['link1'];
if( $link ): 
    $link_url = $link['url'];
    $link_title = $link['title'];
    $link_target = $link['target'] ? $link['target'] : '_self';
    ?>
    <a class="m-1" href="<?php echo esc_url( $link_url ); ?>" target="<?php echo esc_attr( $link_target ); ?>"><button><?php echo esc_html( $link_title ); ?></button></a>
      <?php endif; ?>
               <?php 
$link = $args['link2'];
if( $link ): 
    $link_url = $link['url'];
    $link_title = $link['title'];
    $link_target = $link['target'] ? $link['target'] : '_self';
    ?>
    <a class="m-1" href="<?php echo esc_url( $link_url ); ?>" target="<?php echo esc_attr( $link_target ); ?>"><button class="secondary"><?php echo esc_html( $link_title ); ?></button></a>
      <?php endif; ?>
                </div>
</div>
</div>
       </div>
		</div>
            </div>  
      </section>
