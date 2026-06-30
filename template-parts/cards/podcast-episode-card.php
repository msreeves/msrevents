<?php
/**
 * Single podcast episode card for the archive grid.
 *
 * @package msrevents
 *
 * @var array $args {
 *     @type WP_Term|null $podcaster Podcaster term for show context.
 * }
 */

$podcaster = isset( $args['podcaster'] ) && $args['podcaster'] instanceof WP_Term ? $args['podcaster'] : null;
$audio_url = (string) get_field( 'file' );
$series    = get_field( 'series' );
$episode   = get_field( 'episode' );
$runtime   = (string) get_field( 'runtime' );
$badges    = array();

if ( $series ) {
	$badges[] = sprintf(
		/* translators: %d: series number */
		__( 'Series %d', 'msrevents' ),
		(int) $series
	);
}
if ( $episode ) {
	$badges[] = sprintf(
		/* translators: %d: episode number */
		__( 'Episode %d', 'msrevents' ),
		(int) $episode
	);
}
if ( $runtime ) {
	$badges[] = $runtime;
}
?>
<div class="col-lg-6">
	<article <?php post_class( 'podcast-episode-card panel msr-reveal msr-reveal--up' ); ?>>
		<?php if ( $podcaster ) : ?>
			<p class="podcast-episode-card__show small mb-2"><?php echo esc_html( $podcaster->name ); ?></p>
		<?php endif; ?>
		<?php if ( $badges ) : ?>
			<p class="podcast-episode-card__badges small mb-2">
				<?php echo esc_html( implode( ' · ', $badges ) ); ?>
			</p>
		<?php endif; ?>
		<h2 class="h5 podcast-episode-card__title mb-2"><?php the_title(); ?></h2>
		<?php if ( get_field( 'summary' ) ) : ?>
			<div class="podcast-episode-card__summary msr-rich-text mb-3"><?php msrevents_render_rich_text( get_field( 'summary' ) ); ?></div>
		<?php endif; ?>
		<?php if ( '' !== $audio_url ) : ?>
			<div class="podcast-episode-card__player">
				<audio class="podcast-episode-card__audio" controls preload="none" src="<?php echo esc_url( $audio_url ); ?>">
					<a href="<?php echo esc_url( $audio_url ); ?>"><?php esc_html_e( 'Download audio', 'msrevents' ); ?></a>
				</audio>
			</div>
		<?php endif; ?>
	</article>
</div>
