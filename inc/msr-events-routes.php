<?php
/**
 * Post single helpers — related stories and meta.
 *
 * @package msrevents
 */

/**
 * Related posts in shared categories.
 *
 * @param int $post_id Post ID.
 * @param int $limit   Max posts.
 * @return WP_Post[]
 */
function msrevents_get_related_posts( $post_id = 0, $limit = 3 ) {
	$post_id    = $post_id ? (int) $post_id : (int) get_the_ID();
	$categories = wp_get_post_categories( $post_id );

	if ( ! $categories ) {
		return array();
	}

	$query = new WP_Query(
		array(
			'post_type'           => 'post',
			'post_status'         => 'publish',
			'posts_per_page'      => $limit,
			'post__not_in'        => array( $post_id ),
			'category__in'        => $categories,
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
			'orderby'             => 'date',
			'order'               => 'DESC',
		)
	);

	$posts = $query->posts;
	wp_reset_postdata();

	if ( is_array( $posts ) && $posts ) {
		return $posts;
	}

	if ( ! $categories ) {
		return array();
	}

	$fallback = new WP_Query(
		array(
			'post_type'           => 'post',
			'post_status'         => 'publish',
			'posts_per_page'      => $limit,
			'post__not_in'        => array( $post_id ),
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
			'orderby'             => 'date',
			'order'               => 'DESC',
		)
	);

	$posts = $fallback->posts;
	wp_reset_postdata();

	return is_array( $posts ) ? $posts : array();
}

/**
 * Related stories grid below a post single.
 *
 * @param int $post_id Post ID.
 * @return void
 */
function msrevents_render_post_related_stories( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : (int) get_the_ID();
	$posts   = msrevents_get_related_posts( $post_id, 3 );

	if ( ! $posts ) {
		return;
	}
	?>
	<section class="events-post-related msr-reveal" aria-labelledby="events-post-related-heading-<?php echo esc_attr( (string) $post_id ); ?>">
		<header class="events-post-related__header text-center mb-4">
			<h2 class="h5 events-post-related__title mb-1" id="events-post-related-heading-<?php echo esc_attr( (string) $post_id ); ?>">
				<?php esc_html_e( 'Related stories', 'msrevents' ); ?>
			</h2>
			<p class="events-post-related__lead small mb-0">
				<?php esc_html_e( 'More hub coverage from the same topic area.', 'msrevents' ); ?>
			</p>
		</header>
		<div class="row g-3 justify-content-center msr-card-grid">
			<?php
			global $post;
			foreach ( $posts as $related_post ) {
				$post = $related_post; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
				setup_postdata( $post );
				echo '<div class="col-md-4">';
				get_template_part( 'template-parts/cards/post-card' );
				echo '</div>';
			}
			wp_reset_postdata();
			?>
		</div>
		<div class="text-center mt-3">
			<a class="btn btn-outline-primary btn-sm" href="<?php echo esc_url( msrevents_get_page_url( 'topics', '/topics/' ) ); ?>">
				<?php esc_html_e( 'Browse all topics', 'msrevents' ); ?>
			</a>
		</div>
	</section>
	<?php
}

/**
 * Published podcast episode count.
 *
 * @return int
 */
function msrevents_get_podcast_episode_count() {
	$query = new WP_Query(
		array(
			'post_type'      => 'podcast',
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'fields'         => 'ids',
		)
	);

	return (int) $query->found_posts;
}

/**
 * Podcast archive hero band.
 *
 * @return void
 */
function msrevents_render_podcasts_hero() {
	$count = msrevents_get_podcast_episode_count();
	?>
	<section class="events-podcasts-hero panel msr-reveal" aria-labelledby="events-podcasts-hero-heading">
		<div class="events-podcasts-hero__inner text-center">
			<p class="events-podcasts-hero__eyebrow mb-2"><?php esc_html_e( 'MSR Events audio', 'msrevents' ); ?></p>
			<h1 class="h2 events-podcasts-hero__title mb-2" id="events-podcasts-hero-heading"><?php esc_html_e( 'Podcasts', 'msrevents' ); ?></h1>
			<p class="events-podcasts-hero__lead lead mb-3">
				<?php esc_html_e( 'Programme conversations and delegate insights — demonstration episodes for portfolio review.', 'msrevents' ); ?>
			</p>
			<?php if ( $count > 0 ) : ?>
				<p class="events-podcasts-hero__meta small mb-3">
					<?php
					printf(
						/* translators: %d: episode count */
						esc_html( _n( '%d published episode', '%d published episodes', $count, 'msrevents' ) ),
						(int) $count
					);
					?>
				</p>
			<?php endif; ?>
			<a class="btn btn-outline-primary events-podcasts-hero__cta" href="<?php echo esc_url( msrevents_get_page_url( 'topics', '/topics/' ) ); ?>">
				<?php esc_html_e( 'Browse topics', 'msrevents' ); ?>
			</a>
		</div>
	</section>
	<?php
}

/**
 * Suppress leaderboard adverts on 404 — avoids extra network noise in audits.
 *
 * @param bool $show Whether to show header/footer advert carousels.
 * @return bool
 */
function msrevents_filter_leaderboard_ads_on_404( $show ) {
	if ( is_404() ) {
		return false;
	}

	return (bool) $show;
}
add_filter( 'msrevents_show_leaderboard_ads', 'msrevents_filter_leaderboard_ads_on_404' );
