/**
 * Close mobile offcanvas after in-panel navigation.
 */
document.addEventListener( 'DOMContentLoaded', function () {
	const panel = document.getElementById( 'msrEventsMobileNav' );
	if ( ! panel || typeof bootstrap === 'undefined' ) {
		return;
	}

	const desktop = window.matchMedia( '(min-width: 992px)' );

	panel.querySelectorAll( 'a[href]' ).forEach( function ( link ) {
		link.addEventListener( 'click', function () {
			if ( desktop.matches ) {
				return;
			}
			const instance = bootstrap.Offcanvas.getInstance( panel );
			if ( instance ) {
				instance.hide();
			}
		} );
	} );
} );
