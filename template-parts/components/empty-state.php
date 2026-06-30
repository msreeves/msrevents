<?php
/**
 * Unified empty-state panel for archives, listings, and search.
 *
 * @package msrevents
 *
 * @var array $args {
 *     @type string $context search|archive|listing|gallery.
 *     @type string $title   Heading.
 *     @type string $message Lead copy.
 *     @type bool   $inline  Compact inline variant.
 *     @type bool   $search  Show site search form.
 *     @type array  $links   Helpful link buttons.
 * }
 */

$context = isset( $args['context'] ) ? sanitize_key( (string) $args['context'] ) : 'listing';
$title   = isset( $args['title'] ) ? (string) $args['title'] : '';
$message = isset( $args['message'] ) ? (string) $args['message'] : '';
$inline  = ! empty( $args['inline'] );
$search  = ! empty( $args['search'] );
$links   = isset( $args['links'] ) && is_array( $args['links'] ) ? $args['links'] : array();

if ( '' === $title ) {
	switch ( $context ) {
		case 'search':
			$title = __( 'No results for that search', 'msrevents' );
			break;
		case 'archive':
			$title = __( 'Nothing published here yet', 'msrevents' );
			break;
		case 'gallery':
			$title = __( 'Gallery coming soon', 'msrevents' );
			break;
		default:
			$title = __( 'Nothing to show yet', 'msrevents' );
	}
}

if ( '' === $message ) {
	switch ( $context ) {
		case 'search':
			$message = __( 'Try different keywords or browse programme stories from the hub home.', 'msrevents' );
			$search  = true;
			break;
		case 'archive':
			$message = __( 'Check back after the next programme update, or return to the hub home.', 'msrevents' );
			break;
		case 'gallery':
			$message = __( 'Gallery images will appear here when added in the page editor.', 'msrevents' );
			break;
		default:
			$message = __( 'Content will appear here when published in the admin.', 'msrevents' );
	}
}

if ( ! $links && ! $inline ) {
	$links = msrevents_get_empty_state_default_links();
}

$classes = 'msr-empty-state';
if ( $inline ) {
	$classes .= ' msr-empty-state--inline';
}
?>
<div class="<?php echo esc_attr( $classes ); ?>" data-msr-empty-state="<?php echo esc_attr( $context ); ?>" role="status">
	<?php if ( ! $inline ) : ?>
	<div class="panel text-center">
	<?php endif; ?>
		<?php if ( $title ) : ?>
		<p class="msr-empty-state__title<?php echo $inline ? ' mb-1' : ' h5 mb-2'; ?>"><?php echo esc_html( $title ); ?></p>
		<?php endif; ?>
		<?php if ( $message ) : ?>
		<p class="msr-empty-state__message<?php echo $inline ? ' small mb-0' : ' lead mb-0'; ?>"><?php echo esc_html( $message ); ?></p>
		<?php endif; ?>
		<?php if ( $search && ! $inline ) : ?>
			<?php get_template_part( 'template-parts/forms/site-search' ); ?>
		<?php endif; ?>
		<?php if ( $links && ! $inline ) : ?>
		<nav class="msr-empty-state__links d-flex flex-wrap gap-2 justify-content-center mt-3" aria-label="<?php esc_attr_e( 'Helpful links', 'msrevents' ); ?>">
			<?php foreach ( $links as $link ) : ?>
				<?php if ( empty( $link['url'] ) ) { continue; } ?>
				<a class="btn btn-outline-primary btn-sm" href="<?php echo esc_url( $link['url'] ); ?>">
					<?php echo esc_html( $link['title'] ?? '' ); ?>
				</a>
			<?php endforeach; ?>
		</nav>
		<?php endif; ?>
	<?php if ( ! $inline ) : ?>
	</div>
	<?php endif; ?>
</div>
