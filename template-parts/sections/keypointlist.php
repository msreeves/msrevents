<?php
/**
 * ACF: Flexible Content > Layouts > Key Point list
 *
 * Grid markup aligned with msrawards keypointlist (panel cards, col-xl-3).
 *
 * @package msrevents
 */

$columns = is_array( $args['keypoint'] ?? null ) ? $args['keypoint'] : array();
if ( ! $columns ) {
	return;
}
?>

<section class="events-keypoints">
	<div class="container">
		<div class="row">
			<?php
			foreach ( $columns as $column ) :
				$heading      = $column['title'] ?? '';
				$icon_class   = msrevents_sanitize_fa_icon_class( $column['icon_class'] ?? '' );
				$icon         = $icon_class ? '' : msrevents_acf_image_url( $column['icon'] ?? '' );
				$number       = $column['number'] ?? '';
				$introduction = $column['introduction'] ?? '';
				if ( '' === trim( (string) $heading ) && '' === trim( (string) $number ) ) {
					continue;
				}
				?>
			<div class="col-xl-3 mx-auto">
				<div class="post panel">
					<?php if ( $icon_class || $icon ) : ?>
					<div class="icon" aria-hidden="true">
						<?php if ( $icon_class ) : ?>
						<i class="<?php echo esc_attr( $icon_class ); ?>"></i>
						<?php else : ?>
						<img src="<?php echo esc_url( $icon ); ?>" alt="" />
						<?php endif; ?>
					</div>
					<?php endif; ?>
					<div class="listing-text text-center">
						<?php if ( $number ) : ?>
						<p class="count h2 display-4 mb-2"><?php echo esc_html( $number ); ?></p>
						<?php endif; ?>
						<?php if ( $heading ) : ?>
						<h3 class="h5"><?php echo esc_html( $heading ); ?></h3>
						<?php endif; ?>
						<?php if ( $introduction ) : ?>
						<div class="msr-rich-text"><?php msrevents_render_rich_text( $introduction ); ?></div>
						<?php endif; ?>
					</div>
				</div>
			</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
