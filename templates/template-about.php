<?php
/**
 * Template Name: About template
 *
 * @package msrevents
 */

get_header();

$lead         = msrevents_get_about_lead();
$page_content = '';

if ( have_posts() ) {
	while ( have_posts() ) {
		the_post();
		if ( has_excerpt() ) {
			$lead = wp_strip_all_tags( get_the_excerpt() );
		}
		if ( get_the_content() !== '' ) {
			ob_start();
			the_content();
			$page_content = ob_get_clean();
		}
	}
}

$programmes = msrevents_get_about_programme_cards();
?>
<main id="site-content" class="site-main events-about-page">
	<div class="container">
		<header class="events-about-page__header panel text-center mb-4 msr-reveal">
			<?php the_title( '<h1 class="entry-title mb-2">', '</h1>' ); ?>
			<p class="lead events-about-page__lead mb-0"><?php echo esc_html( $lead ); ?></p>
		</header>

		<?php if ( $page_content ) : ?>
			<div class="events-about-page__intro panel msr-rich-text mb-4">
				<?php echo $page_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- filtered post content. ?>
			</div>
		<?php endif; ?>

		<section class="events-about-page__programmes mb-4" aria-labelledby="events-about-programmes-heading">
			<header class="text-center mb-3">
				<h2 class="h4" id="events-about-programmes-heading"><?php esc_html_e( 'Programme map', 'msrevents' ); ?></h2>
				<p class="small text-muted mb-0"><?php echo esc_html( msrevents_get_about_programmes_intro() ); ?></p>
			</header>
			<div class="row g-3 justify-content-center">
				<?php foreach ( $programmes as $card ) : ?>
					<div class="col-md-4">
						<div class="events-about-page__card panel h-100">
							<h3 class="h6 mb-2"><?php echo esc_html( $card['title'] ); ?></h3>
							<p class="small mb-3"><?php echo esc_html( $card['copy'] ); ?></p>
							<a class="btn btn-outline-primary btn-sm" href="<?php echo esc_url( $card['url'] ); ?>">
								<?php echo esc_html( $card['cta'] ); ?>
							</a>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</section>

		<section class="events-about-page__disclaimer panel mb-4" aria-labelledby="events-about-disclaimer-heading">
			<h2 class="h5 mb-2" id="events-about-disclaimer-heading"><?php esc_html_e( 'Demonstration notice', 'msrevents' ); ?></h2>
			<p class="small mb-0"><?php echo esc_html( msrevents_get_about_disclaimer() ); ?></p>
		</section>

		<?php
		if ( function_exists( 'msrevents_render_ecosystem_band' ) ) {
			msrevents_render_ecosystem_band();
		}
		?>
	</div>
</main>
<?php
get_footer();
