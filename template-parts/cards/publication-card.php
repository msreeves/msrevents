<?php
/**
 * Publication listing card.
 *
 * @package msrevents
 */

$file_parts = msrevents_get_publication_file_parts();
$media_args = array();
if ( $file_parts['url'] ) {
	$media_args = array(
		'link_url'    => $file_parts['url'],
		'link_target' => '_blank',
	);
}
?>
<div class="col-lg-6 col-xl-4">
	<article <?php post_class( 'publication-card post panel msr-reveal msr-reveal--up' ); ?>>
		<?php
		if ( has_post_thumbnail() ) {
			msrevents_render_card_media( null, 'medium_large', $media_args );
		} else {
			?>
			<div class="msr-card-media msr-card-media--placeholder publication-card__placeholder" aria-hidden="true">
				<span class="msr-card-media__placeholder-icon"><i class="fa-solid fa-file-pdf" aria-hidden="true"></i></span>
			</div>
			<?php
		}
		?>
		<div class="publication-card__body listing-text">
			<?php if ( $file_parts['extension'] ) : ?>
				<p class="publication-card__type small mb-2"><?php echo esc_html( $file_parts['extension'] ); ?></p>
			<?php endif; ?>
			<h2 class="h4 publication-card__title"><?php the_title(); ?></h2>
			<?php if ( get_field( 'summary' ) ) : ?>
				<p class="publication-card__summary"><?php echo esc_html( wp_trim_words( wp_strip_all_tags( (string) get_field( 'summary' ) ), 28, '…' ) ); ?></p>
			<?php endif; ?>
			<?php if ( $file_parts['url'] ) : ?>
				<a class="btn btn-primary btn-sm" href="<?php echo esc_url( $file_parts['url'] ); ?>" target="_blank" rel="noopener noreferrer">
					<?php echo esc_html( $file_parts['label'] ); ?>
				</a>
			<?php endif; ?>
		</div>
	</article>
</div>
