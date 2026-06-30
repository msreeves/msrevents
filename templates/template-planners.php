<?php
/**
 * Template Name: Planners template
 *
 * Planner / delegate journey preview for portfolio demonstration.
 *
 * @package msrevents
 */

get_header();
?>
<main id="site-content" class="site-main events-planner-page">
	<section>
		<div class="container">
			<div class="panel text-center mb-4">
				<?php the_title( '<h1>', '</h1>' ); ?>
				<p class="lead"><?php esc_html_e( 'Planner guidance for exploring MSR Events programmes, routing, and lifecycle timelines before wiring live registration flows.', 'msrevents' ); ?></p>
				<?php the_content(); ?>
				<?php get_template_part( 'template-parts/forms/site-search' ); ?>
			</div>
			<?php
			if ( function_exists( 'msrevents_render_planner_journey' ) ) {
				msrevents_render_planner_journey();
			}
			if ( function_exists( 'msrevents_render_programme_timeline' ) ) {
				msrevents_render_programme_timeline();
			}
			if ( function_exists( 'msrevents_render_programme_router_trust' ) ) {
				msrevents_render_programme_router_trust();
			}
			?>
		</div>
	</section>
</main>
<?php
get_footer();
