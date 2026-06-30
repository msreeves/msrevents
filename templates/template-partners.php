<?php
/**
 * Template Name: Partners template
 *
 * @package msrevents
 */

get_header();
?>
<main id="site-content">
<section class="partner msrevents-partners-page events-archive-listing">
	<div class="container">
		<div class="panel">
			<?php the_title( '<h1>', '</h1>' ); ?>
			<p class="lead"><?php esc_html_e( 'Supporters helping deliver MSR Events programmes across Awards, Seminars, and hub coverage.', 'msrevents' ); ?></p>
			<?php the_content(); ?>
			<?php get_template_part( 'template-parts/forms/site-search' ); ?>
		</div>
		<?php msrevents_render_partner_tier_grid(); ?>
		<?php
		if ( function_exists( 'msrevents_render_ecosystem_band' ) ) {
			msrevents_render_ecosystem_band();
		}
		?>
	</div>
</section>
</main>
<?php
get_footer();
