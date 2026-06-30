<?php
/**
 * The main template file
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package msrevents
 */

get_header();
?>

<main id="site-content">
<?php if ( msrevents_is_programme_home() ) : ?>
	<?php get_template_part( 'template-parts/sections/home-hero' ); ?>
	<?php
	$sponsors = get_field( 'main_sponsor', 'option' );
	if ( $sponsors ) {
		get_template_part(
			'template-parts/components/main-sponsor-bar',
			null,
			array( 'sponsors' => $sponsors )
		);
	}
	?>
	<?php
	if ( function_exists( 'msrevents_render_programme_stats' ) ) {
		msrevents_render_programme_stats();
	}
	if ( function_exists( 'msrevents_render_featured_events' ) ) {
		msrevents_render_featured_events();
	}
	?>
	<?php
	$sections = get_field( 'add_sections' );

	if ( is_array( $sections ) ) :
		foreach ( $sections as $index => $section ) :
			if ( ! is_array( $section ) || empty( $section['acf_fc_layout'] ) ) {
				continue;
			}
			if ( function_exists( 'msrevents_hydrate_flexible_section' ) ) {
				$section = msrevents_hydrate_flexible_section( $section, (int) $index );
			}
			$template = str_replace( '_', '-', $section['acf_fc_layout'] );
			get_template_part( 'template-parts/sections/' . $template, '', $section );
		endforeach;
	endif;
	?>
	<?php
	if ( function_exists( 'msrevents_render_programme_timeline' ) ) {
		msrevents_render_programme_timeline();
	}
	if ( function_exists( 'msrevents_render_ecosystem_band' ) ) {
		msrevents_render_ecosystem_band();
	}
	?>
<?php else : ?>
	<section>
		<div class="container">
			<div class="panel">
				<h1><?php the_title(); ?></h1>
				<?php the_content(); ?>
			</div>
		</div>
	</section>
<?php endif; ?>
</main>
<?php
get_footer();
