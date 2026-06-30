<?php
/**
 * Events hub content helpers — taxonomy flatten, category links, empty states.
 *
 * @package msrevents
 */

/**
 * Normalize ACF taxonomy field values to category term IDs.
 *
 * @param mixed $value Term ID, WP_Term, or array of either.
 * @return int[]
 */
function msrevents_normalize_category_term_ids( $value ) {
	if ( $value instanceof WP_Term ) {
		return array( (int) $value->term_id );
	}
	if ( is_numeric( $value ) ) {
		$id = (int) $value;
		return $id > 0 ? array( $id ) : array();
	}
	if ( ! is_array( $value ) ) {
		return array();
	}
	$ids = array();
	foreach ( $value as $item ) {
		if ( $item instanceof WP_Term ) {
			$ids[] = (int) $item->term_id;
		} elseif ( is_array( $item ) && isset( $item['term_id'] ) ) {
			$ids[] = (int) $item['term_id'];
		} elseif ( is_numeric( $item ) ) {
			$ids[] = (int) $item;
		}
	}
	return array_values( array_unique( array_filter( $ids ) ) );
}

/**
 * Flatten taxonomy values to term IDs before ACF calls intval() on them.
 *
 * @param mixed $value Raw or formatted taxonomy value.
 * @return mixed
 */
function msrevents_acf_flatten_taxonomy_value_to_ids( $value ) {
	if ( $value instanceof WP_Term ) {
		return (int) $value->term_id;
	}
	if ( ! is_array( $value ) ) {
		return $value;
	}
	$ids = array();
	foreach ( $value as $item ) {
		if ( $item instanceof WP_Term ) {
			$ids[] = (int) $item->term_id;
		} elseif ( is_array( $item ) && isset( $item['term_id'] ) ) {
			$ids[] = (int) $item['term_id'];
		} elseif ( is_numeric( $item ) ) {
			$ids[] = (int) $item;
		}
	}
	if ( ! $ids ) {
		return $value;
	}
	$ids = array_values( array_unique( array_filter( $ids ) ) );
	return 1 === count( $ids ) ? $ids[0] : $ids;
}

/**
 * Prevent ACF taxonomy fields from passing WP_Term objects into acf_get_valid_terms().
 *
 * @param mixed $value   Field value.
 * @param mixed $post_id Post ID.
 * @param array $field   Field config.
 * @return mixed
 */
function msrevents_acf_taxonomy_load_value_as_ids( $value, $post_id, $field ) {
	unset( $post_id, $field );
	return msrevents_acf_flatten_taxonomy_value_to_ids( $value );
}
add_filter( 'acf/load_value/type=taxonomy', 'msrevents_acf_taxonomy_load_value_as_ids', 5, 3 );

/**
 * Category link markup for programme post cards.
 *
 * @param int|null $post_id Post ID.
 * @param string   $depth   all|child-only.
 * @return string HTML links (escaped).
 */
function msrevents_get_post_category_links_html( $post_id = null, $depth = 'all' ) {
	$post_id    = null === $post_id ? get_the_ID() : (int) $post_id;
	$categories = get_the_category( $post_id );
	if ( ! $categories ) {
		return '';
	}

	$exclude = msrevents_get_excluded_sponsored_category_ids();
	$links   = array();

	foreach ( $categories as $category ) {
		if ( ! $category instanceof WP_Term ) {
			continue;
		}
		if ( in_array( (int) $category->term_id, $exclude, true ) ) {
			continue;
		}
		if ( 'child-only' === $depth && ! $category->parent ) {
			continue;
		}
		if ( 'all' === $depth && ! $category->parent ) {
			$links[] = sprintf(
				'<a class="events-topic-chip" href="%s" title="%s"><span>%s</span></a>',
				esc_url( get_category_link( $category->term_id ) ),
				esc_attr(
					sprintf(
						/* translators: %s: category name */
						__( 'View all posts in %s', 'msrevents' ),
						$category->name
					)
				),
				esc_html( $category->name )
			);
			continue;
		}
		if ( 'all' === $depth && $category->parent ) {
			$links[] = sprintf(
				'<a class="events-topic-chip" href="%s" title="%s"><span>%s</span></a>',
				esc_url( get_category_link( $category->term_id ) ),
				esc_attr(
					sprintf(
						/* translators: %s: category name */
						__( 'View all posts in %s', 'msrevents' ),
						$category->name
					)
				),
				esc_html( $category->name )
			);
		}
		if ( 'child-only' === $depth && $category->parent ) {
			$links[] = sprintf(
				'<a class="events-topic-chip" href="%s"><span>%s</span></a>',
				esc_url( get_category_link( $category->term_id ) ),
				esc_html( $category->name )
			);
		}
	}

	return implode( ' ', $links );
}

/**
 * Default helpful links for empty-state panels.
 *
 * @return array<int, array{title: string, url: string}>
 */
function msrevents_get_empty_state_default_links() {
	$links = array(
		array(
			'title' => __( 'Home', 'msrevents' ),
			'url'   => home_url( '/' ),
		),
		array(
			'title' => __( 'Our events', 'msrevents' ),
			'url'   => msrevents_get_page_url( 'our-events', '/our-events/' ),
		),
		array(
			'title' => __( 'Topics', 'msrevents' ),
			'url'   => msrevents_get_page_url( 'topics', '/topics/' ),
		),
		array(
			'title' => __( 'For planners', 'msrevents' ),
			'url'   => msrevents_get_page_url( 'for-planners', '/for-planners/' ),
		),
	);

	return array_values(
		array_filter(
			$links,
			static function ( $link ) {
				return ! empty( $link['url'] );
			}
		)
	);
}

/**
 * Unified empty-state shell for archives, listings, and search.
 *
 * @param array $args {
 *     @type string $context  search|archive|listing|gallery.
 *     @type string $title    Heading.
 *     @type string $message  Lead copy.
 *     @type bool   $inline   Compact inline variant (filter tabs).
 *     @type bool   $search   Show site search form.
 *     @type array  $links    Helpful link buttons.
 * }
 * @return void
 */
function msrevents_render_empty_state( $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'context' => 'listing',
			'title'   => '',
			'message' => '',
			'inline'  => false,
			'search'  => false,
			'links'   => array(),
		)
	);

	get_template_part( 'template-parts/components/empty-state', null, $args );
}

/**
 * Sponsor tier labels for partner listings.
 *
 * @return array<string, string>
 */
function msrevents_get_sponsor_tiers() {
	return array(
		'main'      => __( 'Main sponsor', 'msrevents' ),
		'gold'      => __( 'Gold partner', 'msrevents' ),
		'silver'    => __( 'Silver partner', 'msrevents' ),
		'supporter' => __( 'Supporter', 'msrevents' ),
	);
}

/**
 * Partner sponsor tier slug.
 *
 * @param int|null $post_id Partner post ID.
 * @return string
 */
function msrevents_get_partner_tier( $post_id = null ) {
	$post_id = null === $post_id ? get_the_ID() : (int) $post_id;
	if ( $post_id <= 0 ) {
		return 'supporter';
	}

	$tier  = function_exists( 'get_field' ) ? get_field( 'sponsor_tier', $post_id ) : '';
	$tier  = sanitize_key( (string) $tier );
	$tiers = msrevents_get_sponsor_tiers();

	return isset( $tiers[ $tier ] ) ? $tier : 'supporter';
}

/**
 * Published partners grouped by sponsor tier (ordered main → supporter).
 *
 * @return array<string, WP_Post[]>
 */
function msrevents_get_partners_grouped_by_tier() {
	$tiers  = array_keys( msrevents_get_sponsor_tiers() );
	$groups = array_fill_keys( $tiers, array() );

	$query = new WP_Query(
		array(
			'post_type'      => 'partner',
			'posts_per_page' => -1,
			'orderby'        => 'title',
			'order'          => 'ASC',
			'post_status'    => 'publish',
		)
	);

	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			$tier                = msrevents_get_partner_tier( get_the_ID() );
			$groups[ $tier ][] = get_post();
		}
		wp_reset_postdata();
	}

	return $groups;
}

/**
 * Render partner cards grouped by sponsor tier.
 *
 * @return void
 */
function msrevents_render_partner_tier_grid() {
	$groups = msrevents_get_partners_grouped_by_tier();
	$labels = msrevents_get_sponsor_tiers();
	$total  = 0;

	foreach ( $groups as $posts ) {
		$total += count( $posts );
	}

	if ( $total <= 0 ) {
		msrevents_render_empty_state(
			array(
				'context' => 'listing',
				'title'   => __( 'No partners published yet', 'msrevents' ),
				'message' => __( 'Partner logos will appear here when supporters are published in the admin.', 'msrevents' ),
			)
		);
		return;
	}

	foreach ( $labels as $tier => $label ) {
		$posts = $groups[ $tier ];
		if ( ! $posts ) {
			continue;
		}
		?>
		<section class="events-partner-tier" aria-labelledby="<?php echo esc_attr( 'events-partner-tier-' . $tier ); ?>">
			<h2 class="events-partner-tier__heading h5 text-center" id="<?php echo esc_attr( 'events-partner-tier-' . $tier ); ?>">
				<?php echo esc_html( $label ); ?>
			</h2>
			<div class="row g-3 justify-content-center msr-card-grid align-items-stretch">
				<?php
				foreach ( $posts as $partner_post ) {
					if ( ! $partner_post instanceof WP_Post ) {
						continue;
					}
					global $post;
					$post = $partner_post; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
					setup_postdata( $post );
					get_template_part( 'template-parts/cards/partner-card' );
				}
				wp_reset_postdata();
				?>
			</div>
		</section>
		<?php
	}
}

/**
 * Planner / delegate journey preview for portfolio demonstration.
 *
 * @return void
 */
function msrevents_render_planner_journey() {
	$steps = array(
		array(
			'title' => __( 'Explore programmes', 'msrevents' ),
			'copy'  => __( 'Review featured events, Awards categories, and Seminars sessions to understand what each programme offers.', 'msrevents' ),
		),
		array(
			'title' => __( 'Plan your visit', 'msrevents' ),
			'copy'  => __( 'Use lifecycle timelines and routing links to decide which subsites and resources to follow before registration opens.', 'msrevents' ),
		),
		array(
			'title' => __( 'Register and attend', 'msrevents' ),
			'copy'  => __( 'Delegates and partners register through programme-specific flows — demonstration copy until live ticketing is connected.', 'msrevents' ),
		),
		array(
			'title' => __( 'Recap and resources', 'msrevents' ),
			'copy'  => __( 'Post-event stories, Atlas Briefing coverage, and programme highlights extend value beyond the live window.', 'msrevents' ),
		),
	);
	?>
	<section class="events-planner-journey" aria-labelledby="events-planner-journey-heading">
		<header class="text-center mb-4">
			<h2 id="events-planner-journey-heading" class="h4 events-planner-journey__title mb-2">
				<?php esc_html_e( 'Planner journey', 'msrevents' ); ?>
			</h2>
			<p class="events-planner-journey__lead mb-0">
				<?php esc_html_e( 'Delegate and partner guidance for portfolio review — replace with live registration mechanics before a production events season.', 'msrevents' ); ?>
			</p>
		</header>
		<ol class="events-planner-journey__steps list-unstyled mb-4">
			<?php foreach ( $steps as $index => $step ) : ?>
				<li class="events-planner-journey__step panel">
					<p class="events-planner-journey__step-label small text-uppercase mb-1">
						<?php
						printf(
							/* translators: %d: step number */
							esc_html__( 'Step %d', 'msrevents' ),
							(int) $index + 1
						);
						?>
					</p>
					<h3 class="h6 events-planner-journey__step-title mb-2"><?php echo esc_html( $step['title'] ); ?></h3>
					<p class="small events-planner-journey__step-copy mb-0"><?php echo esc_html( $step['copy'] ); ?></p>
				</li>
			<?php endforeach; ?>
		</ol>
		<nav class="events-planner-journey__cta events-ctas d-flex flex-wrap gap-2 justify-content-center" aria-label="<?php esc_attr_e( 'Planner guidance', 'msrevents' ); ?>">
			<a class="btn btn-primary btn-sm" href="<?php echo esc_url( msrevents_get_page_url( 'our-events', '/our-events/' ) ); ?>">
				<?php esc_html_e( 'Browse events', 'msrevents' ); ?>
			</a>
			<a class="btn btn-outline-primary btn-sm" href="<?php echo esc_url( msrevents_get_page_url( 'topics', '/topics/' ) ); ?>">
				<?php esc_html_e( 'Read stories', 'msrevents' ); ?>
			</a>
			<a class="btn btn-outline-primary btn-sm" href="<?php echo esc_url( home_url( '/event/msrawards/' ) ); ?>">
				<?php esc_html_e( 'MSR Awards', 'msrevents' ); ?>
			</a>
		</nav>
	</section>
	<?php
}
