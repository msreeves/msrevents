<?php
/**
 * ACF: Flexible Content > Layouts > Content Section
 *
 * @package msrevents
 */

$sections = isset( $args['content'] ) && is_array( $args['content'] ) ? $args['content'] : array();
?>

<section>
   <div class="container">
      <?php foreach ( $sections as $section ) :
		$video_html   = msrevents_render_video_embed( $section['video'] ?? '' );
		$heading      = $section['title'] ?? '';
		$image        = msrevents_acf_image_url( $section['image'] ?? '' );
		$introduction = $section['introduction'] ?? '';
		$layout       = $section['layout'] ?? '';
		?>
        <?php if ( 'fullwidth' === $layout ) { ?>
      <div class="row g-0">
         <div class="col-sm-12">
            <div class="panel">
                  <h2 class="msr-reveal text-center"><?php echo esc_html( $heading ); ?></h2>
                  <div class="msr-reveal msr-rich-text"><?php msrevents_render_rich_text( $introduction ); ?></div>
                    <?php if ( $video_html ) : ?>
                  <div class="listing-video">
                     <?php echo $video_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_kses_post in helper ?>
                  </div>
                  <?php elseif ( $image ) : ?>
                  <div class="listing-image">
                     <img src="<?php echo esc_url( $image ); ?>" alt=""/>
                  </div>
                  <?php elseif ( ! empty( trim( (string) ( $section['video'] ?? '' ) ) ) ) : ?>
                  <p class="msr-video-unavailable" role="status"><?php esc_html_e( 'Video is temporarily unavailable. Refresh the page or check back later.', 'msrevents' ); ?></p>
                  <?php endif; ?>
            </div>
         </div>
      </div>
           <?php } elseif ( 'videoimagetext' === $layout ) { ?>
      <div class="row g-0 mb-5">
         <div class="col-xl-6 col-lg-6">
            <div class="panel">
                  <?php if ( $video_html ) : ?>
                  <div class="listing-video">
                     <?php echo $video_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                  </div>
                  <?php elseif ( $image ) : ?>
                  <div class="listing-image">
                     <img src="<?php echo esc_url( $image ); ?>" alt=""/>
                  </div>
                  <?php elseif ( ! empty( trim( (string) ( $section['video'] ?? '' ) ) ) ) : ?>
                  <p class="msr-video-unavailable" role="status"><?php esc_html_e( 'Video is temporarily unavailable. Refresh the page or check back later.', 'msrevents' ); ?></p>
                  <?php endif; ?>
            </div>
         </div>
         <div class="col-xl-6 col-lg-6">
            <div class="panel">
               <div class="my-auto text-center">
                  <h2 class="msr-reveal text-center"><?php echo esc_html( $heading ); ?></h2>
                  <div class="msr-reveal msr-rich-text"><?php msrevents_render_rich_text( $introduction ); ?></div>
               </div>
            </div>
         </div>
      </div>
      <?php } elseif ( 'textimagevideo' === $layout ) { ?>
      <div class="row g-0">
         <div class="col-xl-6 col-lg-6">
            <div class="panel">
               <div class="my-auto text-center">
                  <h2 class="msr-reveal text-center"><?php echo esc_html( $heading ); ?></h2>
                  <div class="msr-reveal msr-rich-text"><?php msrevents_render_rich_text( $introduction ); ?></div>
               </div>
            </div>
         </div>
         <div class="col-xl-6 col-lg-6">
            <div class="panel">
                  <?php if ( $video_html ) : ?>
                  <div class="listing-video">
                     <?php echo $video_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                  </div>
                  <?php elseif ( $image ) : ?>
                  <div class="listing-image">
                     <img src="<?php echo esc_url( $image ); ?>" alt=""/>
                  </div>
                  <?php elseif ( ! empty( trim( (string) ( $section['video'] ?? '' ) ) ) ) : ?>
                  <p class="msr-video-unavailable" role="status"><?php esc_html_e( 'Video is temporarily unavailable. Refresh the page or check back later.', 'msrevents' ); ?></p>
                  <?php endif; ?>
            </div>
         </div>
      </div>
   <?php } ?>
      <?php endforeach; ?>
   </div>
</section>
