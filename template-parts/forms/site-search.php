<?php
/**
 * Site search form partial.
 *
 * @package msrevents
 *
 * @var array $args {
 *     @type string $input_id Unique input id.
 *     @type bool   $compact  Compact padding for header shell.
 * }
 */

$input_id     = isset( $args['input_id'] ) ? sanitize_html_class( (string) $args['input_id'] ) : sanitize_html_class( get_stylesheet() . '-site-search' );
$compact      = ! empty( $args['compact'] );
$wrapper_cls  = 'msr-site-search' . ( $compact ? ' msr-site-search--compact' : ' p-5' );
$active_type  = function_exists( 'msrevents_get_active_search_type_filter' ) ? msrevents_get_active_search_type_filter() : '';
?>
<div class="<?php echo esc_attr( $wrapper_cls ); ?>">
	<form role="search" method="get" class="searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php esc_attr_e( 'Site search', 'msrevents' ); ?>">
		<div class="input-group flex-wrap flex-md-nowrap">
			<label class="screen-reader-text" for="<?php echo esc_attr( $input_id ); ?>"><?php esc_html_e( 'Search', 'msrevents' ); ?></label>
			<input class="form-control" type="search" name="s" id="<?php echo esc_attr( $input_id ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'Search…', 'msrevents' ); ?>" autocomplete="off" />
			<?php if ( '' !== $active_type ) : ?>
				<input type="hidden" name="msr_ptype" value="<?php echo esc_attr( $active_type ); ?>" />
			<?php endif; ?>
			<input class="btn btn-primary" type="submit" value="<?php esc_attr_e( 'Search', 'msrevents' ); ?>" />
		</div>
	</form>
</div>
