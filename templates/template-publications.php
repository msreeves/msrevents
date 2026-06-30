<?php
/**
 * Template Name: Publications Template
 *
 * @package msrevents
 */

get_header();
?>
<main id="site-content">
<section class="events-publications-page">
	<div class="container">
		<header class="events-publications-page__hero panel msr-reveal">
			<?php the_title( '<h1 class="events-publications-page__title">', '</h1>' ); ?>
			<?php if ( get_the_content() ) : ?>
				<div class="events-publications-page__intro msr-rich-text"><?php the_content(); ?></div>
			<?php else : ?>
				<p class="lead events-publications-page__lead mb-0">
					<?php echo esc_html( msrevents_get_publications_page_lead() ); ?>
				</p>
			<?php endif; ?>
		</header>
		<?php msrevents_render_publications_grid( -1 ); ?>
	</div>
</section>
</main>
<?php
get_footer();
