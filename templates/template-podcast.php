<?php
/**
 * Template Name: Podcast Template
 *
 * @package WordPress
 * @subpackage msrevents
 * @since msrevents 1.0
 */
get_header();
?>
<main id="site-content">
<section class="events-archive-listing">
	<div class="container">
		<?php msrevents_render_podcasts_hero(); ?>
		<?php if ( have_posts() ) : ?>
			<?php while ( have_posts() ) : ?>
				<?php the_post(); ?>
				<?php if ( get_the_content() !== '' ) : ?>
					<div class="panel mb-4 msr-rich-text">
						<?php the_content(); ?>
					</div>
				<?php endif; ?>
			<?php endwhile; ?>
		<?php endif; ?>
		<?php get_template_part( 'template-parts/forms/site-search' ); ?>
		<?php get_template_part( 'template-parts/listings/podcast-by-podcaster' ); ?>
	</div>
</section>
</main>
<?php
get_footer();
