/**
 * Mobile nav — offcanvas close + submenu accordions.
 */
document.addEventListener( 'DOMContentLoaded', function () {
	const panel = document.getElementById( 'site-header-mobile-nav' );
	if ( ! panel || typeof bootstrap === 'undefined' ) {
		return;
	}

	const desktop = window.matchMedia( '(min-width: 992px)' );

	panel.querySelectorAll( '.events-nav__toggle' ).forEach( function ( button ) {
		button.addEventListener( 'click', function ( event ) {
			event.preventDefault();
			if ( desktop.matches ) {
				return;
			}

			const parent = button.closest( '.events-nav__item--has-children' );
			const submenu = parent ? parent.querySelector( ':scope > .events-nav__submenu' ) : null;
			if ( ! submenu ) {
				return;
			}

			const isOpen = button.getAttribute( 'aria-expanded' ) === 'true';
			const nextOpen = ! isOpen;
			button.setAttribute( 'aria-expanded', nextOpen ? 'true' : 'false' );

			const parentLink = parent.querySelector( ':scope > .events-nav__row > .events-nav__link[aria-haspopup]' );
			if ( parentLink ) {
				parentLink.setAttribute( 'aria-expanded', nextOpen ? 'true' : 'false' );
			}

			submenu.classList.toggle( 'is-open', nextOpen );
		} );
	} );

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
