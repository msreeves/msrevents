<?php
/**
 * Template Name: Events Template
 *
 * @package msrevents
 */

get_header();

$archive_state   = msrevents_get_events_archive_filter_state();
$filtered_events = msrevents_query_filtered_events( $archive_state );
?>
<main id="site-content">
<section class="msr-events-listing msr-events-archive-page">
	<div class="container">
		<header class="events-archive-page__hero panel msr-reveal">
			<?php the_title( '<h1 class="events-archive-page__title">', '</h1>' ); ?>
			<p class="lead events-archive-page__lead mb-0">
				<?php echo esc_html( msrevents_get_events_archive_intro() ); ?>
			</p>
		</header>
		<?php msrevents_render_programme_lifecycle_band(); ?>
		<?php
		if ( function_exists( 'msrevents_render_companion_archive_band' ) ) {
			msrevents_render_companion_archive_band();
		}
		?>
		<?php msrevents_render_events_archive_filters( $archive_state, count( $filtered_events ) ); ?>
		<?php get_template_part( 'template-parts/forms/site-search' ); ?>
		<?php msrevents_render_events_archive_listing( $archive_state, $filtered_events ); ?>
	</div>
</section>
</main>
<?php
get_footer();
