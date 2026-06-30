<?php
/**
 * Awards filter bar — markup helpers (no Bootstrap nav-link / nav-pills).
 *
 * @package msrevents
 */

/**
 * Open filter bar list.
 *
 * @param string $aria_label Accessible name.
 * @param bool   $tablist    Add role="tablist" for in-page tabs.
 * @return void
 */
function msrevents_filter_bar_open( $aria_label, $tablist = false ) {
	printf(
		'<nav class="msr-filter-bar events-filter-bar" aria-label="%s"><ul class="events-filter-bar__list"%s>',
		esc_attr( $aria_label ),
		$tablist ? ' role="tablist"' : ''
	);
}

/**
 * Close filter bar list.
 *
 * @return void
 */
function msrevents_filter_bar_close() {
	echo '</ul></nav>';
}

/**
 * Filter link (navigation to another URL).
 *
 * @param string $label  Link text.
 * @param string $url    Destination.
 * @param bool   $active Current page.
 * @return void
 */
function msrevents_filter_bar_link( $label, $url, $active = false ) {
	echo '<li class="events-filter-bar__item">';
	if ( $active ) {
		printf(
			'<span class="events-filter-bar__link is-active" aria-current="page">%s</span>',
			esc_html( $label )
		);
	} else {
		printf(
			'<a class="events-filter-bar__link" href="%s">%s</a>',
			esc_url( $url ),
			esc_html( $label )
		);
	}
	echo '</li>';
}

/**
 * Filter tab (Bootstrap tab toggle, in-page).
 *
 * @param string $label   Tab label.
 * @param string $tab_id  Button id (…-tab).
 * @param string $pane_id Panel id target.
 * @param bool   $active  Selected tab.
 * @return void
 */
function msrevents_filter_bar_tab( $label, $tab_id, $pane_id, $active = false ) {
	$classes = 'events-filter-bar__tab';
	if ( $active ) {
		$classes .= ' is-active active';
	}
	echo '<li class="events-filter-bar__item" role="presentation">';
	printf(
		'<button type="button" class="%s" id="%s" data-bs-toggle="tab" data-bs-target="#%s" role="tab" aria-controls="%s" aria-selected="%s">%s</button>',
		esc_attr( $classes ),
		esc_attr( $tab_id ),
		esc_attr( $pane_id ),
		esc_attr( $pane_id ),
		$active ? 'true' : 'false',
		esc_html( $label )
	);
	echo '</li>';
}

/**
 * Active-filter summary below the tab bar (updated by filter-tabs.js).
 *
 * @param string $label Active filter label.
 * @return void
 */
function msrevents_filter_bar_status( $label ) {
	printf(
		'<p class="events-filter-status" role="status" aria-live="polite" data-msr-filter-status data-filter-label="%s">',
		esc_attr( $label )
	);
	echo '<span class="events-filter-status__prefix">' . esc_html__( 'Showing:', 'msrevents' ) . '</span> ';
	printf(
		'<strong class="events-filter-status__label">%s</strong>',
		esc_html( $label )
	);
	echo '</p>';
}
