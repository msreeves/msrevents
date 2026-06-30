<?php
/**
 * Partner / sponsor archive card.
 *
 * @package msrevents
 *
 * @var array $args {
 *     @type bool $compact Inline logo chip (home partner strip).
 * }
 */

$compact     = ! empty( $args['compact'] );
$link        = msrevents_get_acf_link_parts( get_field( 'link' ) );
$tier_slug   = msrevents_get_partner_tier( get_the_ID() );
$tier_labels = msrevents_get_sponsor_tiers();
$tier_label  = $tier_labels[ $tier_slug ] ?? '';
$media_args  = array();
if ( $link['url'] ) {
	$media_args = array(
		'link_url'    => $link['url'],
		'link_target' => $link['target'],
	);
}
?>

<div class="<?php echo esc_attr( $compact ? 'events-partner-chip' : 'mx-auto mb-3 col-md-6 col-lg-4' ); ?>">
	<article <?php post_class( $compact ? 'partner-card partner-card--compact' : 'partner-card panel msr-reveal msr-reveal--up' ); ?>>
		<div class="partner-listing-image events-logo-tile<?php echo $compact ? ' events-logo-tile--compact' : ''; ?>">
			<?php
			if ( $link['url'] ) {
				msrevents_render_card_media( null, 'medium', $media_args );
			} else {
				msrevents_render_card_media( null, 'medium' );
			}
			?>
		</div>
		<?php if ( ! $compact && $tier_label ) : ?>
			<p class="partner-card__tier small text-center mb-2">
				<span class="partner-card__tier-badge"><?php echo esc_html( $tier_label ); ?></span>
			</p>
		<?php endif; ?>
	</article>
</div>
