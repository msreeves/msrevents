<?php
/**
 * Podcast archive grouped by podcaster with a modern episode grid.
 *
 * @package msrevents
 */

$podcaster_terms = get_terms(
	array(
		'taxonomy'   => 'podcaster',
		'hide_empty' => true,
	)
);
if ( is_wp_error( $podcaster_terms ) || ! is_array( $podcaster_terms ) ) {
	$podcaster_terms = array();
}

if ( ! $podcaster_terms ) {
	msrevents_render_empty_state(
		array(
			'context' => 'listing',
			'title'   => __( 'No podcasts published yet', 'msrevents' ),
			'message' => __( 'Episodes will appear here when podcasters and episodes are published.', 'msrevents' ),
		)
	);
	return;
}

foreach ( $podcaster_terms as $podcaster_term ) {
	if ( ! $podcaster_term instanceof WP_Term ) {
		continue;
	}

	$podcaster_query = new WP_Query(
		array(
			'post_type'      => 'podcast',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'menu_order title',
			'order'          => 'ASC',
			'tax_query'      => array(
				array(
					'taxonomy' => 'podcaster',
					'field'    => 'slug',
					'terms'    => array( $podcaster_term->slug ),
					'operator' => 'IN',
				),
			),
		)
	);

	if ( ! $podcaster_query->have_posts() ) {
		wp_reset_postdata();
		continue;
	}
	?>
	<section class="podcast-show panel msr-reveal" aria-labelledby="podcast-show-<?php echo esc_attr( (string) $podcaster_term->term_id ); ?>">
		<header class="podcast-show__header">
			<div class="podcast-show__brand">
				<?php
				$image = get_field( 'cat_thumb', $podcaster_term );
				if ( is_array( $image ) && ! empty( $image['sizes']['medium'] ) ) :
					$thumb = (string) $image['sizes']['medium'];
					$alt   = ! empty( $image['alt'] ) ? (string) $image['alt'] : $podcaster_term->name;
					?>
					<div class="podcast-show__thumb">
						<img src="<?php echo esc_url( $thumb ); ?>" alt="<?php echo esc_attr( $alt ); ?>" width="96" height="96" loading="lazy" />
					</div>
				<?php else : ?>
					<div class="podcast-show__thumb podcast-show__thumb--placeholder" aria-hidden="true">
						<i class="fa-solid fa-microphone-lines" aria-hidden="true"></i>
					</div>
				<?php endif; ?>
				<div class="podcast-show__intro">
					<h2 class="h4 podcast-show__title mb-2" id="podcast-show-<?php echo esc_attr( (string) $podcaster_term->term_id ); ?>">
						<?php echo esc_html( $podcaster_term->name ); ?>
					</h2>
					<?php if ( $podcaster_term->description ) : ?>
						<div class="podcast-show__description msr-rich-text mb-0"><?php echo wp_kses_post( $podcaster_term->description ); ?></div>
					<?php endif; ?>
				</div>
			</div>
		</header>
		<div class="row g-4 msr-card-grid events-podcasts-grid">
			<?php
			while ( $podcaster_query->have_posts() ) :
				$podcaster_query->the_post();
				get_template_part(
					'template-parts/cards/podcast-episode-card',
					null,
					array(
						'podcaster' => $podcaster_term,
					)
				);
			endwhile;
			?>
		</div>
	</section>
	<?php
	wp_reset_postdata();
}
