<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package msrevents
 */

get_header();
?>

	<main id="primary" class="site-main">

		<?php if ( have_posts() ) : ?>

			<section>
				<div class="container">
						<div class="panel">
					<h1><?php single_cat_title(); ?></h1>
					<h3><?php the_archive_description(); ?></h3>
					 <?php get_template_part( 'inc/controllers/searchbar' ); ?>
				</div>

			<div class="container">
			<div class="row">
			<?php
			/* Start the Loop */
			while ( have_posts() ) :
				the_post();
				get_template_part( 'templates/partials/post-listing/listing-posts' );
				

			endwhile;

			echo '<section>';
			the_posts_pagination( array(
'mid_size' => 2,
'prev_text' => __( 'Previous', 'textdomain' ),
'next_text' => __( 'Next', 'textdomain' ),
) );
			echo '</section>';

		else :

			get_template_part( 'template-parts/content', 'none' );

		endif;
		?>
</div>
	</main><!-- #main -->

<?php
get_footer();
