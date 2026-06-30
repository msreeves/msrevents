<?php
/**
 * ACF: Flexible Content > Layouts > Listing Partners
 *
 * @package msrevents
 */

$heading      = isset( $args['title'] ) ? (string) $args['title'] : '';
$introduction = isset( $args['introduction'] ) ? (string) $args['introduction'] : '';
?>

<section class="partner msrevents-partner-list">
	<div class="container">
		<div class="panel">
			<?php if ( $heading ) : ?>
			<h2 class="msr-reveal"><?php echo esc_html( $heading ); ?></h2>
			<?php endif; ?>
			<?php if ( $introduction ) : ?>
			<p class="lead msr-reveal"><?php echo wp_kses_post( $introduction ); ?></p>
			<?php endif; ?>
		</div>
		<?php msrevents_render_partner_tier_grid(); ?>
	</div>
</section>
