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
	<header class="entry-header">
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
        <?php get_template_part('templates/partials/featured-image'); ?>
	</header><!-- .entry-header -->

	<div class="entry-content">
		<?php
		the_content(
			sprintf(
				wp_kses(
					/* translators: %s: Name of current post. Only visible to screen readers */
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
	</div><!-- .entry-content -->
    <?php
if (is_singular('post'))
{

    get_template_part('templates/partials/related');

}

/*
 * Output comments wrapper if it's a post, or if comments are open,
 * or if there's a comment number – and check for password.
*/
if ((is_single() || is_page()) && (comments_open() || get_comments_number()) && !post_password_required())
{
?>

		<div class="comments-wrapper section-inner">

			<?php comments_template(); ?>

		</div><!-- .comments-wrapper -->

		<?php
}
?>
</article><!-- #post-<?php the_ID(); ?> -->
</section>
