<?php
/**
 * Template Name: Posts Template
 *
 * @package WordPress
 * @subpackage msrevents
 * @since msrsandbox 1.0
 */
get_header();
?>
<main id="site-content">
<section class="events-archive-listing">
	<div class="container">
		<?php the_title( '<h1>', '</h1>' ); ?>
		<?php the_content(); ?>
		<?php get_template_part( 'template-parts/forms/site-search' ); ?>
		<?php
		get_template_part(
			'templates/partials/filter-tabs',
			'',
			array(
				'taxonomy'      => 'category',
				'post_type'     => 'post',
				'all_label'     => __( 'All', 'msrevents' ),
				'listing_all'   => 'template-parts/cards/post-card',
				'listing_term'  => 'template-parts/cards/post-card',
				'parent'        => 0,
				'query_args'    => array(
					'orderby' => 'date',
					'order'   => 'ASC',
				),
				'empty_message' => __( 'No posts found in this category.', 'msrevents' ),
			)
		);
		?>
	</div>
</section>
</main>
<?php
get_footer();
