<?php
/**
 * ACF: Flexible Content > Layouts > Banner
 * UPDATE
 * @package WordPress
 */

 $slider = $args['advert'];
?>

<section class="bg-white">
<div id="Carousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="3000">
        <?php setup_postdata($banner); ?>
                  <div class="carousel-inner">
                            <?php $count = 0; ?>
                            <?php foreach ($slider as $slide) : ?>
                                <?php setup_postdata($slide->ID); ?>
                                <div class="carousel-item <?php echo ($count == 0) ? 'active' : ''; ?>">
                                   <div class="row g-0">
                    	   <div class="col-md-12">
               		<div class="panel">
				<div class="my-auto">
				<h2 class="text-center">Advertisement</h2>
		  </div>
		  </div>
                </div>
                    <div class="col-md-12"> 
                <div class="panel">
				<div class="my-auto mx-auto">
                                  <?php 
$link = get_field('link', $slide->ID);
if( $link ): 
    $link_url = $link['url'];
    $link_title = $link['title'];
    $link_target = $link['target'] ? $link['target'] : '_self';
    ?>
    <a href="<?php echo esc_url( $link_url ); ?>" target="<?php echo esc_attr( $link_target ); ?>"><?php echo esc_html( $link_title ); ?>
    <?php $image = wp_get_attachment_image_src(
                                                   get_post_thumbnail_id(
                                                       $slide->ID
                                                   ),
                                                   "single-post-thumbnail"
                                               ); ?>
                                                <img src="<?php echo $image[0]; ?>" alt=""> </a>
</a>
<?php endif; ?>
                                            </div>
                            </div>
                            </div>
                        </div>

                                </div>
                                <?php $count++; ?>
                            <?php endforeach; ?>
                        </div>
    <?php 
    // Reset the global post object so that the rest of the page works correctly.
    wp_reset_postdata(); ?>
</section>