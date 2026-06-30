<?php
/**
 * Programme news / topic post card.
 *
 * @package msrevents
 *
 * @var array $args {
 *     @type string $category_depth all|child-only.
 *     @type string $variant        default|search.
 * }
 */

$category_depth = isset( $args['category_depth'] ) ? (string) $args['category_depth'] : 'all';
$variant        = isset( $args['variant'] ) ? (string) $args['variant'] : 'default';
$category_links = msrevents_get_post_category_links_html( get_the_ID(), $category_depth );
?>
<div class="col-xl-4 col-lg-4">
	<article <?php post_class( 'post-card post panel msr-reveal msr-reveal--up' ); ?>>
		<?php if ( 'search' === $variant ) : ?>
			<span class="events-search-result-type"><?php echo esc_html( msrevents_get_search_result_type_label() ); ?></span>
		<?php endif; ?>
		<?php msrevents_render_card_media(); ?>
		<div class="post-card__body listing-text">
			<?php if ( $category_links ) : ?>
				<p class="post-card__categories"><?php echo $category_links; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
			<?php endif; ?>
			<?php if ( msrevents_is_sponsored_post() ) : ?>
				<span class="sponsored"><?php esc_html_e( 'This is Sponsored content', 'msrevents' ); ?></span>
			<?php endif; ?>
			<?php if ( 'search' === $variant ) : ?>
				<h2 class="h4 post-card__title"><?php echo msrevents_search_title_highlight(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h2>
				<div class="post-card__excerpt"><?php echo msrevents_search_excerpt_highlight(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
			<?php else : ?>
				<h2 class="h4 post-card__title"><?php the_title(); ?></h2>
				<p class="post-card__excerpt"><?php echo esc_html( wpse_custom_excerpts( 30 ) ); ?></p>
			<?php endif; ?>
			<a class="btn btn-primary btn-sm" href="<?php echo esc_url( get_permalink() ); ?>">
				<?php esc_html_e( 'Read more', 'msrevents' ); ?>
			</a>
		</div>
	</article>
</div>
