<?php
/**
 * Events hub ecosystem outbound links — Awards, Seminars, Atlas Briefing.
 *
 * @package msrevents
 */

function msrevents_get_ecosystem_link_defaults() {
	return array(
		'awards'     => array(
			'label'       => __( 'MSR Awards', 'msrevents' ),
			'url'         => 'http://msrevents.local:8888/msrawards/',
			'description' => __( 'Recognition programme with nominees, categories, judging transparency, and ceremony routing.', 'msrevents' ),
			'cta'         => __( 'Visit MSR Awards', 'msrevents' ),
		),
		'seminars'   => array(
			'label'       => __( 'MSR Seminars', 'msrevents' ),
			'url'         => 'http://msrevents.local:8888/msrseminars/',
			'description' => __( 'Delegate learning programme with agenda, panelists, and post-event resources.', 'msrevents' ),
			'cta'         => __( 'Visit MSR Seminars', 'msrevents' ),
		),
		'publishing' => array(
			'label'       => __( 'Atlas Briefing insights', 'msrevents' ),
			'url'         => 'http://127.0.0.1:8888/sites/wp/msrpublishing/insights/',
			'description' => __( 'Year-round commentary and resource library content linked from MSR programme surfaces.', 'msrevents' ),
			'cta'         => __( 'Read Atlas Briefing', 'msrevents' ),
		),
	);
}

function msrevents_get_ecosystem_option_keys() {
	return array(
		'awards'     => 'msr_events_ecosystem_awards_url',
		'seminars'   => 'msr_events_ecosystem_seminars_url',
		'publishing' => 'msr_events_ecosystem_publishing_url',
	);
}

function msrevents_get_ecosystem_links() {
	$defaults = msrevents_get_ecosystem_link_defaults();
	$links    = array();

	foreach ( $defaults as $slug => $item ) {
		$url = function_exists( 'msrevents_get_programme_url_option' )
			? msrevents_get_programme_url_option( $slug )
			: '';
		if ( '' === $url ) {
			$url = $item['url'];
		}
		if ( '' === $url ) {
			continue;
		}
		$links[] = array_merge( array( 'key' => $slug ), $item, array( 'url' => $url ) );
	}

	return $links;
}

function msrevents_render_ecosystem_band() {
	$links = msrevents_get_ecosystem_links();
	if ( ! $links ) {
		return;
	}
	?>
	<section class="events-ecosystem msr-reveal" aria-labelledby="events-ecosystem-heading">
		<div class="container">
			<header class="events-ecosystem__header text-center mb-4">
				<h2 id="events-ecosystem-heading" class="h4 events-ecosystem__title mb-2">
					<?php echo esc_html( msrevents_get_ecosystem_band_title() ); ?>
				</h2>
				<p class="events-ecosystem__lead mb-0">
					<?php echo esc_html( msrevents_get_ecosystem_band_lead() ); ?>
				</p>
			</header>
			<div class="row g-3 justify-content-center">
				<?php foreach ( $links as $link ) : ?>
					<div class="col-md-4">
						<div class="events-ecosystem__card h-100">
							<h3 class="h6 events-ecosystem__card-title mb-2"><?php echo esc_html( $link['label'] ); ?></h3>
							<p class="small events-ecosystem__card-copy mb-3"><?php echo esc_html( $link['description'] ); ?></p>
							<a class="btn btn-outline-primary btn-sm" href="<?php echo esc_url( $link['url'] ); ?>">
								<?php echo esc_html( $link['cta'] ); ?>
							</a>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php
}
