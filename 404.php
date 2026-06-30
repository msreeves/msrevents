<?php
/**
 * 404 — events hub chrome with search and helpful links.
 *
 * @package msrevents
 */

get_header();

$home       = home_url( '/' );
$our_events = msrevents_get_page_url( 'our-events', '/our-events/' );
$topics     = msrevents_get_page_url( 'topics', '/topics/' );
$planners   = msrevents_get_page_url( 'for-planners', '/for-planners/' );
?>
<main id="site-content" class="events-error-page">
	<div class="container py-5 text-center">
		<p class="events-error-page__code display-1 mb-2" aria-hidden="true">404</p>
		<h1 class="h2 mb-3"><?php esc_html_e( 'Page not found', 'msrevents' ); ?></h1>
		<p class="text-muted mb-4 events-error-page__lead">
			<?php esc_html_e( 'That URL is not part of the MSR Events hub, or it may have moved.', 'msrevents' ); ?>
		</p>

		<div class="events-error-page__search mb-4">
			<?php get_template_part( 'template-parts/forms/site-search' ); ?>
		</div>

		<?php
		get_template_part(
			'template-parts/components/empty-state',
			null,
			array(
				'context' => 'listing',
				'title'   => __( 'Try a hub route instead', 'msrevents' ),
				'message' => __( 'Browse programme listings, topics, or planner guidance from the links below.', 'msrevents' ),
				'search'  => false,
				'links'   => array(
					array(
						'title' => __( 'Home', 'msrevents' ),
						'url'   => $home,
					),
					array(
						'title' => __( 'Our events', 'msrevents' ),
						'url'   => $our_events,
					),
					array(
						'title' => __( 'Topics', 'msrevents' ),
						'url'   => $topics,
					),
					array(
						'title' => __( 'For planners', 'msrevents' ),
						'url'   => $planners,
					),
				),
			)
		);
		?>
	</div>
</main>
<?php
get_footer();
