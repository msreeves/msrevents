<?php
/**
 * Privacy notice (demo) — slug: privacy.
 *
 * @package msrevents
 */

get_header();
?>
<main id="site-content" class="awards-privacy-page">
	<div class="container py-5">
		<div class="row justify-content-center">
			<div class="col-lg-8">
				<header class="mb-4">
					<h1 class="entry-title"><?php esc_html_e( 'Privacy notice (demonstration)', 'msrevents' ); ?></h1>
					<p class="text-muted"><?php esc_html_e( 'Portfolio placeholder — not legal advice. Replace before production launch.', 'msrevents' ); ?></p>
				</header>
				<?php if ( have_posts() ) : ?>
					<?php
					while ( have_posts() ) {
						the_post();
						if ( get_the_content() !== '' ) {
							?>
							<div class="entry-content">
								<?php the_content(); ?>
							</div>
							<?php
						}
					}
					?>
				<?php endif; ?>
				<div class="awards-privacy-page__demo small text-muted">
					<p><?php esc_html_e( 'MSR Events is a demonstration hub site. Contact forms and registration flows shown in portfolio review do not store or transmit personal data. Connect a privacy policy and consent flow before a live programme season.', 'msrevents' ); ?></p>
				<nav class="awards-privacy-page__links d-flex flex-wrap gap-2" aria-label="<?php esc_attr_e( 'Helpful links', 'msrevents' ); ?>">
					<a class="btn btn-outline-primary btn-sm" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Events home', 'msrevents' ); ?></a>
					<a class="btn btn-outline-primary btn-sm" href="<?php echo esc_url( home_url( '/our-events/' ) ); ?>"><?php esc_html_e( 'Our events', 'msrevents' ); ?></a>
				</nav>
				</div>
			</div>
		</div>
	</div>
</main>
<?php
get_footer();
