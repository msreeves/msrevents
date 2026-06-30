<?php
/**
 * ACF: Flexible Content > Layouts > Call to Action
 *
 * @package msrevents
 */

$heading      = isset( $args['title'] ) ? (string) $args['title'] : '';
$image        = msrevents_acf_image_url( $args['image'] ?? '' );
$introduction = isset( $args['introduction'] ) ? (string) $args['introduction'] : '';

if ( '' === $image && function_exists( 'get_field' ) ) {
	$image = msrevents_acf_image_url( get_field( 'image', 'option' ) );
}

$has_image     = '' !== $image;
$section_class = 'cta' . ( $has_image ? '' : ' cta--no-media' );
?>

<section class="<?php echo esc_attr( $section_class ); ?>">
	<div class="container">
		<div class="panel events-cta-panel">
			<div class="row align-items-center g-4">
				<?php if ( $has_image ) : ?>
				<div class="col-lg-6">
					<div class="events-cta-panel__media">
						<img
							class="events-cta-panel__img"
							src="<?php echo esc_url( $image ); ?>"
							alt=""
							loading="eager"
							decoding="async"
							fetchpriority="low"
						/>
					</div>
				</div>
				<?php endif; ?>
				<div class="<?php echo esc_attr( $has_image ? 'col-lg-6' : 'col-lg-10 mx-auto' ); ?>">
					<div class="events-cta-panel__body text-center">
						<?php if ( $heading ) : ?>
						<h2><?php echo esc_html( $heading ); ?></h2>
						<?php endif; ?>
						<?php if ( $introduction ) : ?>
						<div class="msr-rich-text"><?php msrevents_render_rich_text( $introduction ); ?></div>
						<?php endif; ?>
						<div class="events-ctas ctas d-flex flex-wrap justify-content-center gap-2">
							<?php msrevents_render_cta_link( $args['link1'] ?? null ); ?>
							<?php msrevents_render_cta_link( $args['link2'] ?? null, 'btn btn-outline-primary' ); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
