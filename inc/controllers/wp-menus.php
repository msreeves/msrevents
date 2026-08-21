<?php
/**
 * Navigation menus and primary nav walker.
 *
 * @package msrevents
 */

/**
 * Register navigation menus uses wp_nav_menu.
 */
function msrevents_menus() {

	$locations = array(
		'menu-1'  => __( 'Primary', 'msrevents' ),
		'primary' => __( 'Desktop Horizontal Menu', 'msrevents' ),
		'footer'  => __( 'Footer Menu', 'msrevents' ),
		'social'  => __( 'Social Menu', 'msrevents' ),
	);

	register_nav_menus( $locations );
}

/**
 * Custom nav walker — BEM markup with desktop dropdowns + mobile toggles.
 */
class CSS_Menu_Walker extends Walker {

	/**
	 * @var array<string, string>
	 */
	public $db_fields = array(
		'parent' => 'menu_item_parent',
		'id'     => 'db_id',
	);

	/**
	 * @param string $output Markup.
	 * @param int    $depth  Depth.
	 * @param array  $args   Args.
	 * @return void
	 */
	public function start_lvl( &$output, $depth = 0, $args = array() ) {
		$indent  = str_repeat( "\t", $depth );
		$classes = 0 === $depth ? ' class="events-nav__submenu"' : '';
		$output .= "\n$indent<ul{$classes}>\n";
	}

	/**
	 * @param string $output Markup.
	 * @param int    $depth  Depth.
	 * @param array  $args   Args.
	 * @return void
	 */
	public function end_lvl( &$output, $depth = 0, $args = array() ) {
		$indent  = str_repeat( "\t", $depth );
		$output .= "$indent</ul>\n";
	}

	/**
	 * @param string   $output Markup.
	 * @param WP_Post  $item   Menu item.
	 * @param int      $depth  Depth.
	 * @param stdClass $args   Args.
	 * @param int      $id     Item id.
	 * @return void
	 */
	public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		$indent  = $depth ? str_repeat( "\t", $depth ) : '';
		$classes = empty( $item->classes ) ? array() : (array) $item->classes;

		if ( in_array( 'current-menu-item', $classes, true ) ) {
			$classes[] = 'active';
		}

		$has_children = in_array( 'menu-item-has-children', $classes, true );
		if ( $has_children && 0 === $depth ) {
			$classes[] = 'has-sub';
			$classes[] = 'events-nav__item--has-children';
		}

		$classes[] = 0 === $depth ? 'events-nav__item' : 'events-nav__subitem';

		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

		$item_id = apply_filters( 'nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args );
		$item_id = $item_id ? ' id="' . esc_attr( $item_id ) . '"' : '';

		$output .= $indent . '<li' . $item_id . $class_names . '>';

		$attributes  = ! empty( $item->attr_title ) ? ' title="' . esc_attr( $item->attr_title ) . '"' : '';
		$attributes .= ! empty( $item->target ) ? ' target="' . esc_attr( $item->target ) . '"' : '';
		$attributes .= ! empty( $item->xfn ) ? ' rel="' . esc_attr( $item->xfn ) . '"' : '';
		$attributes .= ! empty( $item->url ) ? ' href="' . esc_attr( $item->url ) . '"' : '';

		$link_class = 0 === $depth ? 'events-nav__link' : 'events-nav__sublink';

		if ( $has_children && 0 === $depth ) {
			$attributes .= ' aria-haspopup="true" aria-expanded="false"';
		}

		$before      = is_object( $args ) ? ( $args->before ?? '' ) : '';
		$after       = is_object( $args ) ? ( $args->after ?? '' ) : '';
		$link_before = is_object( $args ) ? ( $args->link_before ?? '' ) : '';
		$link_after  = is_object( $args ) ? ( $args->link_after ?? '' ) : '';

		$title = apply_filters( 'the_title', $item->title, $item->ID );

		$item_output = $before;

		if ( $has_children && 0 === $depth ) {
			$item_output .= '<div class="events-nav__row">';
		}

		// Inline SVG keeps a square aspect ratio; FA webfont at sub-rem size looks squashed in flex.
		$chevron = '<svg class="events-nav__chevron" width="12" height="12" viewBox="0 0 16 16" aria-hidden="true" focusable="false"><path fill="currentColor" d="M3.2 5.4a.75.75 0 0 1 1.06 0L8 9.14l3.74-3.74a.75.75 0 1 1 1.06 1.06l-4.27 4.27a.75.75 0 0 1-1.06 0L3.2 6.46a.75.75 0 0 1 0-1.06z"/></svg>';

		$item_output .= '<a class="' . esc_attr( $link_class ) . '"' . $attributes . '><span>';
		$item_output .= $link_before . esc_html( $title ) . $link_after;
		$item_output .= '</span>';
		if ( $has_children && 0 === $depth ) {
			$item_output .= $chevron;
		}
		$item_output .= '</a>';

		if ( $has_children && 0 === $depth ) {
			$item_output .= sprintf(
				'<button type="button" class="events-nav__toggle" aria-expanded="false" aria-label="%s">%s</button></div>',
				esc_attr(
					sprintf(
						/* translators: %s: parent menu item title */
						__( 'Show submenu for %s', 'msrevents' ),
						$title
					)
				),
				$chevron
			);
		}

		$item_output .= $after;

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}

	/**
	 * @param string   $output Markup.
	 * @param WP_Post  $item   Menu item.
	 * @param int      $depth  Depth.
	 * @param stdClass $args   Args.
	 * @return void
	 */
	public function end_el( &$output, $item, $depth = 0, $args = array() ) {
		$output .= "</li>\n";
	}
}

add_action( 'init', 'msrevents_menus' );
