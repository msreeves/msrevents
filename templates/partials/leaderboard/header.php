<?php
/**
 * Leaderboard header advert carousel.
 *
 * @package msrevents
 */
?>
<div id="events-carousel-leaderboard-header" class="carousel slide" data-bs-ride="carousel" data-bs-interval="3000">
	<div class="carousel-inner">
		<?php
		$slider = get_posts(
			array(
				'post_type'      => 'advert',
				'posts_per_page' => -1,
				'tax_query'      => array(
					array(
						'taxonomy' => 'location',
						'field'    => 'slug',
						'terms'    => 'header',
					),
				),
			)
		);
		?>
		<?php $count = 0; ?>
		<?php foreach ( $slider as $slide ) : ?>
			<?php setup_postdata( $slide->ID ); ?>
			<div class="carousel-item <?php echo 0 === $count ? 'active' : ''; ?>">
				<div class="row g-0">
					<div class="col-md-4">
						<div class="panel">
							<div class="my-auto">
								<p class="events-advert__label text-center"><?php esc_html_e( 'Advertisement', 'msrevents' ); ?></p>
							</div>
						</div>
					</div>
					<div class="col-md-8">
						<div class="panel">
							<div class="my-auto mx-auto">
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
					</div>
				</div>
			</div>
			<?php ++$count; ?>
		<?php endforeach; ?>
	</div>
</div>
<?php wp_reset_postdata(); ?>
