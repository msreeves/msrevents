<?php
/**
 * Template Name: Nominees Template
 *
 * @package msrevents
 */

get_header();
?>
<main id="site-content" class="site-main">
<section class="people events-archive-listing">
	<div class="container">
		<header class="events-archive-intro panel">
			<?php the_title( '<h1>', '</h1>' ); ?>
			<p class="lead"><?php esc_html_e( 'Browse nominees by award category—demonstration route for portfolio review when nominee content is published on the hub.', 'msrevents' ); ?></p>
		</header>
		<?php
		get_template_part(
			'templates/partials/filter-tabs',
			'',
			array(
				'taxonomy'          => 'award',
				'post_type'         => 'nominee',
				'all_label'         => __( 'All', 'msrevents' ),
				'listing_all'       => 'template-parts/cards/nominee-card',
				'listing_all_args'  => array( 'show_award_terms' => true ),
				'listing_term'      => 'template-parts/cards/nominee-card',
				'listing_term_args' => array( 'show_award_terms' => false ),
				'query_args'        => array(
					'meta_key' => 'name',
					'orderby'  => 'meta_value',
					'order'    => 'ASC',
				),
				'empty_message'     => __( 'No nominees found in this category.', 'msrevents' ),
			)
		);
		?>
		<?php if ( get_the_content() ) : ?>
			<div class="events-archive-supplement panel">
				<?php the_content(); ?>
			</div>
		<?php endif; ?>
		<?php get_template_part( 'template-parts/forms/site-search' ); ?>
	</div>
</section>
</main>
<?php
get_footer();
