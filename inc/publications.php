<?php
/**
 * Publication CPT helpers and listing surfaces.
 *
 * @package msrevents
 */

/**
 * Resolve ACF file field on a publication to download parts.
 *
 * @param mixed $file    Raw ACF file value.
 * @param int   $post_id Publication post ID when $file is null.
 * @return array{url: string, label: string, mime: string, extension: string}
 */
function msrevents_get_publication_file_parts( $file = null, $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : (int) get_the_ID();
	if ( null === $file ) {
		$file = get_field( 'link', $post_id );
	}

	$parts = array(
		'url'       => '',
		'label'     => __( 'Download', 'msrevents' ),
		'mime'      => '',
		'extension' => '',
	);

	if ( ! is_array( $file ) || empty( $file['url'] ) ) {
		return $parts;
	}

	$parts['url']  = esc_url_raw( (string) $file['url'] );
	$parts['mime'] = (string) ( $file['mime_type'] ?? '' );
	$subtype       = strtoupper( (string) ( $file['subtype'] ?? '' ) );

	if ( str_contains( $parts['mime'], 'pdf' ) || 'PDF' === $subtype ) {
		$parts['extension'] = 'PDF';
		$parts['label']     = __( 'Download PDF', 'msrevents' );
	} elseif ( $subtype ) {
		$parts['extension'] = $subtype;
		$parts['label']     = sprintf(
			/* translators: %s: file type, e.g. PDF */
			__( 'Download %s', 'msrevents' ),
			$subtype
		);
	}

	return $parts;
}

/**
 * Query published publications for listings.
 *
 * @param int $limit Max posts; -1 for all.
 * @return WP_Post[]
 */
function msrevents_query_publications( $limit = 6 ) {
	$query = new WP_Query(
		array(
			'post_type'              => 'publication',
			'post_status'            => 'publish',
			'posts_per_page'         => -1 === (int) $limit ? -1 : max( 1, (int) $limit ),
			'orderby'                => 'date',
			'order'                  => 'DESC',
			'no_found_rows'          => true,
			'update_post_meta_cache' => true,
		)
	);

	$posts = $query->posts;
	wp_reset_postdata();

	return is_array( $posts ) ? $posts : array();
}

/**
 * Render a publications card grid.
 *
 * @param int $limit Max cards; -1 for all.
 * @return void
 */
function msrevents_render_publications_grid( $limit = 6 ) {
	$posts = msrevents_query_publications( $limit );

	if ( ! $posts ) {
		msrevents_render_empty_state(
			array(
				'context' => 'listing',
				'title'   => __( 'No publications yet', 'msrevents' ),
				'message' => __( 'Programme guides and resources will appear here when published.', 'msrevents' ),
			)
		);
		return;
	}
	?>
	<div class="row g-4 msr-card-grid events-publications-grid">
		<?php
		global $post;
		foreach ( $posts as $post ) {
			setup_postdata( $post );
			get_template_part( 'template-parts/cards/publication-card' );
		}
		wp_reset_postdata();
		?>
	</div>
	<?php
}

/**
 * Home / section publications band with optional heading.
 *
 * @param array{title?: string, introduction?: string, limit?: int} $args Section args.
 * @return void
 */
function msrevents_render_publications_section( $args = array() ) {
	$heading      = isset( $args['title'] ) ? (string) $args['title'] : '';
	$introduction = isset( $args['introduction'] ) ? (string) $args['introduction'] : '';
	$limit        = isset( $args['limit'] ) ? (int) $args['limit'] : 6;
	?>
	<section class="events-publications msrevents-publication-list" aria-labelledby="events-publications-heading">
		<div class="container">
			<?php if ( $heading || $introduction ) : ?>
			<header class="events-publications__header panel msr-reveal text-center mb-4">
				<?php if ( $heading ) : ?>
					<h2 id="events-publications-heading" class="h4 events-publications__title mb-2"><?php echo esc_html( $heading ); ?></h2>
				<?php endif; ?>
				<?php if ( $introduction ) : ?>
					<div class="events-publications__lead msr-rich-text mb-0"><?php msrevents_render_rich_text( $introduction ); ?></div>
				<?php endif; ?>
			</header>
			<?php endif; ?>
			<?php msrevents_render_publications_grid( $limit ); ?>
		</div>
	</section>
	<?php
}
