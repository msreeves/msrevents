<?php
/**
 * The template for displaying archive pages
 *
 * @package msrevents
 */

get_header();
?>

<main id="primary" class="site-main">
	<section>
		<div class="container">
			<?php if ( have_posts() ) : ?>
			<div class="panel">
				<h1><?php single_cat_title(); ?></h1>
				<?php the_archive_description( '<p class="lead">', '</p>' ); ?>
				<?php get_template_part( 'template-parts/forms/site-search' ); ?>
			</div>
			<div class="row msr-card-grid">
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/cards/post-card' );
				endwhile;
				?>
			</div>
			<nav class="msr-archive-pagination" aria-label="<?php esc_attr_e( 'Archive pages', 'msrevents' ); ?>">
				<?php
				the_posts_pagination(
					array(
						'mid_size'  => 2,
						'prev_text' => __( 'Previous', 'msrevents' ),
						'next_text' => __( 'Next', 'msrevents' ),
					)
				);
				?>
			</nav>
			<?php else : ?>
			<div class="panel">
				<?php
				msrevents_render_empty_state(
					array(
						'context' => 'archive',
					)
				);
				?>
			</div>
			<?php endif; ?>
		</div>
	</section>
</main>

<?php
get_footer();
