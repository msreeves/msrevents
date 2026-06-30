<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package msrevents
 */

?>

<section>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="panel text-center">
		<?php
		$category_links = msrevents_get_post_category_links_html( get_the_ID(), 'all' );
		if ( $category_links ) :
			?>
		<p class="post-card__categories"><?php echo $category_links; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
			<?php
		endif;
		?>
				 <?php
if ( msrevents_is_sponsored_post() ) {
?>
<h3> <i>This is Sponsored content</i></h3>
<?php } ?> 
		<?php
		if ( is_singular() ) :
			the_title( '<h1 class="entry-title">', '</h1>' );
		else :
			the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
		endif;

		if ( 'post' === get_post_type() ) :
			?>
			<p class="entry-meta">
				<i class="fa fa-clock fa-2xl"></i> 
                <?php echo get_the_date(); ?>
		</p><!-- .entry-meta -->
		<?php endif; ?>
			</div><!-- .entry-header -->

	<div class="entry-content">
		     <?php get_template_part('templates/partials/featured-image'); ?>
			 <section>
		<?php
		the_content(
			sprintf(
				wp_kses(
					/* translators: %s: Name of current post. Only visible to screen readers */
					__( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'msrsandbox' ),
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
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'msrsandbox' ),
				'after'  => '</div>',
			)
		);
		?>
		</section>
	</div><!-- .entry-content -->
</article><!-- #post-<?php the_ID(); ?> -->
</section>
