<?php
/**
 * ACF: Flexible Content > Layouts > Banner
 *
 * @package msrevents
 */

$slider = $args['advert'] ?? array();
if ( ! is_array( $slider ) || ! $slider ) {
	return;
}
?>
<section id="advert">
<div id="events-carousel-advert" class="carousel slide" data-bs-ride="carousel" data-bs-interval="3000">
	<div class="carousel-inner">
		<?php $count = 0; ?>
		<?php foreach ( $slider as $slide ) : ?>
			<?php setup_postdata( $slide->ID ); ?>
			<div class="carousel-item <?php echo ( 0 === $count ) ? 'active' : ''; ?>">
				<div class="panel events-advert-panel text-center">
					<p class="events-advert__label mb-3"><?php esc_html_e( 'Advertisement', 'msrevents' ); ?></p>
					<?php
					$link = get_field( 'link', $slide->ID );
					if ( is_array( $link ) && ! empty( $link['url'] ) ) :
						$link_target = ! empty( $link['target'] ) ? (string) $link['target'] : '_self';
						$thumb_id    = (int) get_post_thumbnail_id( $slide->ID );
						$image       = $thumb_id ? wp_get_attachment_image_src( $thumb_id, 'single-post-thumbnail' ) : false;
						$img_url     = ( is_array( $image ) && ! empty( $image[0] ) ) ? (string) $image[0] : '';
						$caption     = $thumb_id ? wp_get_attachment_caption( $thumb_id ) : '';
						$link_label  = ! empty( $link['title'] ) ? (string) $link['title'] : __( 'Sponsored link', 'msrevents' );
						$img_alt     = '' !== (string) $caption ? (string) $caption : $link_label;
						?>
						<a href="<?php echo esc_url( (string) $link['url'] ); ?>" target="<?php echo esc_attr( $link_target ); ?>" aria-label="<?php echo esc_attr( $link_label ); ?>">
							<?php if ( $img_url ) : ?>
								<img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( $img_alt ); ?>" />
							<?php endif; ?>
							<span class="screen-reader-text"><?php echo esc_html( $link_label ); ?></span>
						</a>
					<?php endif; ?>
				</div>
			</div>
			<?php ++$count; ?>
		<?php endforeach; ?>
	</div>
</div>
<?php wp_reset_postdata(); ?>
</section>
