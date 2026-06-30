<?php
/**
 * Search helpers — highlight markup and query guards.
 *
 * @package msrevents
 */

/**
 * Allowed HTML for search highlight spans.
 *
 * @return array<string, array<string, bool>>
 */
function msrevents_search_highlight_allowed_html() {
	return array(
		'strong' => array(
			'class' => array( 'search-highlight' ),
		),
		'p'      => array(),
	);
}

/**
 * Highlight search terms in plain text and return safe HTML.
 *
 * @param string $text Source text.
 * @return string
 */
function msrevents_search_highlight_text( $text ) {
	$query = trim( (string) get_search_query() );
	$text  = (string) $text;
	if ( '' === $query || '' === $text ) {
		return esc_html( $text );
	}

	$parts = preg_split( '/\s+/u', $query, -1, PREG_SPLIT_NO_EMPTY );
	if ( ! $parts ) {
		return esc_html( $text );
	}

	$pattern = '/' . implode(
		'|',
		array_map(
			static function ( $part ) {
				return preg_quote( $part, '/' );
			},
			$parts
		)
	) . '/iu';

	$highlighted = preg_replace( $pattern, '<strong class="search-highlight">$0</strong>', $text );
	if ( ! is_string( $highlighted ) ) {
		return esc_html( $text );
	}

	return wp_kses( $highlighted, msrevents_search_highlight_allowed_html() );
}

/**
 * Highlighted post title for search results.
 *
 * @return string Safe HTML.
 */
function msrevents_search_title_highlight() {
	return msrevents_search_highlight_text( get_the_title() );
}

/**
 * Highlighted excerpt for search results.
 *
 * @return string Safe HTML wrapped in paragraph.
 */
function msrevents_search_excerpt_highlight() {
	return '<p>' . msrevents_search_highlight_text( get_the_excerpt() ) . '</p>';
}

/**
 * Exclude pages from front-end search (nominees/judges remain CPT-driven routes).
 *
 * @return void
 */
function msrevents_exclude_pages_from_search() {
	global $wp_post_types;
	if ( isset( $wp_post_types['page'] ) ) {
		$wp_post_types['page']->exclude_from_search = true;
	}
}
add_action( 'init', 'msrevents_exclude_pages_from_search' );

/**
 * Post types included in hub site search.
 *
 * @return string[]
 */
function msrevents_get_searchable_post_types() {
	$types = array( 'post', 'event', 'podcast' );
	if ( post_type_exists( 'publication' ) ) {
		$types[] = 'publication';
	}

	return $types;
}

/**
 * Search result type filter pills.
 *
 * @return array<string, string> slug => label (empty slug = all).
 */
function msrevents_get_search_type_filters() {
	return array(
		''         => __( 'All results', 'msrevents' ),
		'event'    => __( 'Events', 'msrevents' ),
		'post'     => __( 'Stories', 'msrevents' ),
		'podcast'  => __( 'Podcasts', 'msrevents' ),
	);
}

/**
 * Active search type filter from the query string.
 *
 * @param WP_Query|null $query Optional query (for pre_get_posts).
 * @return string
 */
function msrevents_get_active_search_type_filter( $query = null ) {
	$is_search = false;
	if ( $query instanceof WP_Query ) {
		$is_search = $query->is_search();
	} elseif ( is_search() ) {
		$is_search = true;
	} elseif ( isset( $_GET['s'] ) && '' !== trim( (string) wp_unslash( $_GET['s'] ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$is_search = true;
	}

	if ( ! $is_search ) {
		return '';
	}

	$type    = isset( $_GET['msr_ptype'] ) ? sanitize_key( wp_unslash( $_GET['msr_ptype'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$filters = msrevents_get_search_type_filters();

	return isset( $filters[ $type ] ) ? $type : '';
}

/**
 * Human label for a search result post type.
 *
 * @param WP_Post|int|null $post Post object or ID.
 * @return string
 */
function msrevents_get_search_result_type_label( $post = null ) {
	$post = $post ? get_post( $post ) : get_post();
	if ( ! $post instanceof WP_Post ) {
		return __( 'Result', 'msrevents' );
	}

	$labels = array(
		'event'       => __( 'Event', 'msrevents' ),
		'post'        => __( 'Story', 'msrevents' ),
		'podcast'     => __( 'Podcast', 'msrevents' ),
		'publication' => __( 'Publication', 'msrevents' ),
	);

	return $labels[ $post->post_type ] ?? __( 'Result', 'msrevents' );
}

/**
 * Build a search URL preserving query + optional type filter.
 *
 * @param string $type_slug Filter slug (empty = all).
 * @return string
 */
function msrevents_get_search_type_pill_url( $type_slug = '' ) {
	$args = array(
		's' => get_search_query(),
	);

	if ( '' !== $type_slug ) {
		$args['msr_ptype'] = $type_slug;
	}

	return add_query_arg( $args, home_url( '/' ) );
}

/**
 * Type filter pills for search results.
 *
 * @return void
 */
function msrevents_render_search_type_pills() {
	if ( ! is_search() ) {
		return;
	}

	$filters = msrevents_get_search_type_filters();
	$active  = msrevents_get_active_search_type_filter();

	printf(
		'<nav class="msr-filter-bar events-filter-bar events-search-type-pills" data-msr-search-type-pills aria-label="%s"><ul class="events-filter-bar__list">',
		esc_attr__( 'Filter search results by type', 'msrevents' )
	);
	foreach ( $filters as $slug => $label ) {
		msrevents_filter_bar_link( $label, msrevents_get_search_type_pill_url( $slug ), $slug === $active );
	}
	echo '</ul></nav>';
}

/**
 * Popular demo searches for empty search state.
 *
 * @return void
 */
function msrevents_render_search_popular_terms() {
	if ( ! is_search() ) {
		return;
	}

	$terms = array( 'programme', 'awards', 'seminars' );
	?>
	<nav class="events-search-popular text-center mt-3" aria-label="<?php esc_attr_e( 'Popular searches', 'msrevents' ); ?>">
		<p class="small text-muted mb-2"><?php esc_html_e( 'Popular searches', 'msrevents' ); ?></p>
		<div class="d-flex flex-wrap gap-2 justify-content-center">
			<?php foreach ( $terms as $term ) : ?>
				<a class="btn btn-outline-primary btn-sm events-search-popular__term" href="<?php echo esc_url( add_query_arg( 's', $term, home_url( '/' ) ) ); ?>">
					<?php echo esc_html( $term ); ?>
				</a>
			<?php endforeach; ?>
		</div>
	</nav>
	<?php
}

/**
 * Featured events band below empty search results.
 *
 * @return void
 */
function msrevents_render_search_empty_featured_events() {
	if ( ! is_search() || have_posts() || ! function_exists( 'msrevents_render_featured_events' ) ) {
		return;
	}
	?>
	<div class="events-search-empty-featured mt-4">
		<?php msrevents_render_featured_events(); ?>
	</div>
	<?php
}

/**
 * Limit hub search to programme content types and optional type pill filter.
 *
 * @param WP_Query $query Main query.
 * @return void
 */
function msrevents_search_pre_get_posts( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}

	if ( ! $query->is_search() && '' === trim( (string) $query->get( 's' ) ) ) {
		return;
	}

	$active = msrevents_get_active_search_type_filter( $query );
	if ( '' !== $active ) {
		$query->set( 'post_type', $active );
		return;
	}

	$query->set( 'post_type', msrevents_get_searchable_post_types() );
}
add_action( 'pre_get_posts', 'msrevents_search_pre_get_posts' );

/**
 * Backward-compatible aliases (legacy templates).
 *
 * @deprecated Use msrevents_search_excerpt_highlight().
 */
function search_excerpt_highlight() {
	echo msrevents_search_excerpt_highlight(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * @deprecated Use msrevents_search_title_highlight().
 */
function search_title_highlight() {
	echo msrevents_search_title_highlight(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
