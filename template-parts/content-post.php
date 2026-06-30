<?php
/**
 * Single post content.
 *
 * @package msrevents
 */

$post_id = get_the_ID();
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'events-post-single' ); ?>>
	<header class="events-post-single__header panel text-center mb-4">
		<?php
		$category_links = msrevents_get_post_category_links_html( $post_id, 'all' );
		if ( $category_links ) :
			?>
			<div class="events-post-single__topics mb-2"><?php echo $category_links; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
		<?php endif; ?>

		<?php if ( msrevents_is_sponsored_post( $post_id ) ) : ?>
			<p class="events-post-single__sponsored small mb-2"><?php esc_html_e( 'Sponsored content', 'msrevents' ); ?></p>
		<?php endif; ?>

		<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>

		<p class="events-post-single__meta entry-meta mb-0">
			<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
				<?php echo esc_html( get_the_date() ); ?>
			</time>
		</p>
	</header>

	<?php if ( has_post_thumbnail( $post_id ) ) : ?>
		<div class="events-post-single__media mb-4">
			<?php msrevents_render_card_media( $post_id, 'large' ); ?>
		</div>
	<?php endif; ?>

	<div class="entry-content msr-rich-text panel mb-4">
		<?php
		the_content(
			sprintf(
				wp_kses(
					/* translators: %s: post title */
					__( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'msrevents' ),
					array(
						'span' => array(
							'class' => array(),
						),
					)
				),
				wp_kses_post( get_the_title() )
			)
		);

		wp_link_pages(
			array(
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'msrevents' ),
				'after'  => '</div>',
			)
		);
		?>
	</div>

	<?php msrevents_render_post_related_stories( $post_id ); ?>

	<?php if ( function_exists( 'msrevents_render_ecosystem_band' ) ) : ?>
		<div class="events-post-single__ecosystem mt-4">
			<?php msrevents_render_ecosystem_band(); ?>
		</div>
	<?php endif; ?>
</article>
