<?php
/**
 * Event listing card for Our Events archive.
 *
 * @package msrevents
 */

$post_id          = get_the_ID();
$schedule_summary = msrevents_get_event_schedule_summary( $post_id );
$teaser           = msrevents_get_event_listing_teaser( $post_id );
$link1            = get_field( 'link1', $post_id );
$cta_url          = ( is_array( $link1 ) && ! empty( $link1['url'] ) ) ? (string) $link1['url'] : get_permalink( $post_id );
$cta_label        = ( is_array( $link1 ) && ! empty( $link1['title'] ) ) ? (string) $link1['title'] : __( 'View programme', 'msrevents' );
$cta_target       = ( is_array( $link1 ) && ! empty( $link1['target'] ) ) ? (string) $link1['target'] : '_self';
?>
<div class="col-lg-6 col-xl-4">
	<article <?php post_class( 'event-listing-card panel msr-reveal msr-reveal--up' ); ?>>
		<?php
		if ( has_post_thumbnail( $post_id ) ) {
			msrevents_render_card_media(
				$post_id,
				'medium_large',
				array(
					'link_url'    => $cta_url,
					'link_target' => $cta_target,
					'link_class'  => 'event-listing-card__media-link',
				)
			);
		} else {
			?>
			<div class="msr-card-media msr-card-media--placeholder" aria-hidden="true">
				<span class="msr-card-media__placeholder-icon"><i class="fa-solid fa-calendar-days" aria-hidden="true"></i></span>
			</div>
			<?php
		}
		?>
		<div class="event-listing-card__body listing-text">
			<div class="event-listing-card__meta">
				<?php msrevents_render_event_meta_chips( $post_id ); ?>
				<?php msrevents_render_event_lifecycle_badge( $post_id ); ?>
			</div>
			<h2 class="h4 event-listing-card__title">
				<a href="<?php echo esc_url( $cta_url ); ?>"<?php echo '_blank' === $cta_target ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>>
					<?php the_title(); ?>
				</a>
			</h2>
			<?php if ( $schedule_summary ) : ?>
				<p class="event-listing-card__schedule small mb-2"><?php echo esc_html( $schedule_summary ); ?></p>
			<?php endif; ?>
			<?php if ( $teaser ) : ?>
				<p class="event-listing-card__teaser mb-3"><?php echo esc_html( $teaser ); ?></p>
			<?php endif; ?>
			<a class="btn btn-primary btn-sm" href="<?php echo esc_url( $cta_url ); ?>"<?php echo '_blank' === $cta_target ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>>
				<?php echo esc_html( $cta_label ); ?>
			</a>
		</div>
	</article>
</div>
