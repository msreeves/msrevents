<?php
/**
 * Reusable taxonomy filter tabs + tab panes.
 *
 * @package msrevents
 *
 * @var array $args {
 *     @type string $taxonomy       Taxonomy slug (e.g. category, award).
 *     @type string $post_type      Post type to query.
 *     @type string $all_label      Label for the "all" tab.
 *     @type string $listing_all    Template path for "all" listing cards.
 *     @type string $listing_term   Template path for per-term listing cards.
 *     @type array  $listing_all_args  Args passed to listing_all template part.
 *     @type array  $listing_term_args Args passed to listing_term template part.
 *     @type array  $query_args     Extra WP_Query args (orderby, meta_key, etc.).
 *     @type int    $parent         Parent term ID for hierarchical taxonomies (category only).
 *     @type string $empty_message  Message when a tab has no posts.
 *     @type string $filter_label   Accessible name for the filter bar nav.
 * }
 */

$taxonomy      = isset( $args['taxonomy'] ) ? (string) $args['taxonomy'] : 'category';
$post_type     = isset( $args['post_type'] ) ? (string) $args['post_type'] : 'post';
$all_label     = isset( $args['all_label'] ) ? (string) $args['all_label'] : __( 'All', 'msrevents' );
$listing_all       = isset( $args['listing_all'] ) ? (string) $args['listing_all'] : '';
$listing_term      = isset( $args['listing_term'] ) ? (string) $args['listing_term'] : $listing_all;
$listing_all_args  = isset( $args['listing_all_args'] ) && is_array( $args['listing_all_args'] ) ? $args['listing_all_args'] : array();
$listing_term_args = isset( $args['listing_term_args'] ) && is_array( $args['listing_term_args'] ) ? $args['listing_term_args'] : array();
$query_args    = isset( $args['query_args'] ) && is_array( $args['query_args'] ) ? $args['query_args'] : array();
$parent        = isset( $args['parent'] ) ? (int) $args['parent'] : 0;
$empty_message = isset( $args['empty_message'] ) ? (string) $args['empty_message'] : __( 'No items found in this category.', 'msrevents' );
$filter_label  = isset( $args['filter_label'] ) ? (string) $args['filter_label'] : __( 'Filter listings', 'msrevents' );

$filter_terms = msrevents_get_filter_terms( $taxonomy, $parent );
$tabs_id      = 'msr-filter-' . sanitize_html_class( $post_type . '-' . $taxonomy );
?>
<div class="post-tabs msr-filter-tabs" id="<?php echo esc_attr( $tabs_id ); ?>" data-msr-filter-tabs>
	<?php
	msrevents_filter_bar_open( $filter_label, true );
	msrevents_filter_bar_tab( $all_label, $tabs_id . '-tab-all', $tabs_id . '-pane-all', true );
	foreach ( $filter_terms as $term ) {
		if ( ! ( $term instanceof WP_Term ) ) {
			continue;
		}
		msrevents_filter_bar_tab(
			$term->name,
			$tabs_id . '-tab-' . $term->slug,
			$tabs_id . '-pane-' . $term->slug,
			false
		);
	}
	msrevents_filter_bar_close();
	msrevents_filter_bar_status( $all_label );
	?>

	<div class="tab-content">
		<div
			class="tab-pane fade show active"
			id="<?php echo esc_attr( $tabs_id ); ?>-pane-all"
			role="tabpanel"
			aria-labelledby="<?php echo esc_attr( $tabs_id ); ?>-tab-all"
			tabindex="0"
			data-filter-label="<?php echo esc_attr( $all_label ); ?>"
		>
			<?php
			$all_query = new WP_Query(
				msrevents_filter_tabs_query_args( $post_type, '', $taxonomy, $query_args )
			);
			if ( $all_query->have_posts() ) :
				?>
				<div class="row msr-card-grid">
					<?php
					while ( $all_query->have_posts() ) :
						$all_query->the_post();
						if ( $listing_all ) {
							get_template_part( $listing_all, null, $listing_all_args );
						}
					endwhile;
					wp_reset_postdata();
					?>
				</div>
			<?php else : ?>
				<?php
				msrevents_render_empty_state(
					array(
						'context' => 'listing',
						'message' => $empty_message,
						'inline'  => true,
					)
				);
				?>
			<?php endif; ?>
		</div>

		<?php foreach ( $filter_terms as $term ) : ?>
			<?php
			if ( ! ( $term instanceof WP_Term ) ) {
				continue;
			}
			$pane_id = $tabs_id . '-pane-' . $term->slug;
			$tab_id  = $tabs_id . '-tab-' . $term->slug;
			$term_query = new WP_Query(
				msrevents_filter_tabs_query_args( $post_type, $term->slug, $taxonomy, $query_args )
			);
			?>
			<div
				class="tab-pane fade"
				id="<?php echo esc_attr( $pane_id ); ?>"
				role="tabpanel"
				aria-labelledby="<?php echo esc_attr( $tab_id ); ?>"
				tabindex="0"
				data-filter-label="<?php echo esc_attr( $term->name ); ?>"
			>
				<?php if ( $term_query->have_posts() ) : ?>
					<div class="row msr-card-grid">
						<?php
						while ( $term_query->have_posts() ) :
							$term_query->the_post();
							if ( $listing_term ) {
								get_template_part( $listing_term, null, $listing_term_args );
							}
						endwhile;
						wp_reset_postdata();
						?>
					</div>
				<?php else : ?>
					<?php
					msrevents_render_empty_state(
						array(
							'context' => 'listing',
							'message' => $empty_message,
							'inline'  => true,
						)
					);
					?>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>
	</div>
</div>
