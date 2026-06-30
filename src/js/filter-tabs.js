/**
 * Filter tabs: active-state sync, status summary, hash sync, scroll-into-view.
 */
document.addEventListener('DOMContentLoaded', function () {
	document.querySelectorAll('[data-msr-filter-tabs]').forEach(function (root) {
		var tabControls = root.querySelectorAll('[data-bs-toggle="tab"]');
		if (!tabControls.length) {
			return;
		}

		var status = root.querySelector('[data-msr-filter-status]');
		var prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
		var compactTabs = window.matchMedia('(max-width: 991px)');

		function syncTabActiveState(activeTab) {
			tabControls.forEach(function (control) {
				var isActive = control === activeTab;
				control.classList.toggle('active', isActive);
				control.classList.toggle('is-active', isActive);
				control.setAttribute('aria-selected', isActive ? 'true' : 'false');
			});
		}

		function updateStatusFromPane(pane) {
			if (!status || !pane) {
				return;
			}

			var label = pane.getAttribute('data-filter-label') || '';
			var labelNode = status.querySelector('.events-filter-status__label');

			status.setAttribute('data-filter-label', label);

			if (labelNode) {
				labelNode.textContent = label;
			}
		}

		function activePane() {
			return root.querySelector('.tab-pane.active.show, .tab-pane.active');
		}

		function scrollTabIntoView(tab) {
			if (!tab || !compactTabs.matches) {
				return;
			}
			tab.scrollIntoView({
				inline: 'nearest',
				block: 'nearest',
				behavior: prefersReducedMotion ? 'auto' : 'smooth',
			});
		}

		function activateFromHash() {
			var hash = window.location.hash;
			if (!hash) {
				return;
			}
			var target = root.querySelector('[data-bs-target="' + hash + '"], [href="' + hash + '"]');
			if (!target || typeof bootstrap === 'undefined') {
				return;
			}
			var tab = bootstrap.Tab.getOrCreateInstance(target);
			tab.show();
			scrollTabIntoView(target);
		}

		tabControls.forEach(function (control) {
			control.addEventListener('shown.bs.tab', function (event) {
				syncTabActiveState(event.target);

				var paneSelector =
					event.target.getAttribute('data-bs-target') || event.target.getAttribute('href');
				var pane = paneSelector ? root.querySelector(paneSelector) : null;
				updateStatusFromPane(pane);

				if (paneSelector && paneSelector.charAt(0) === '#') {
					history.replaceState(null, '', paneSelector);
				}
				scrollTabIntoView(event.target);
			});
		});

		syncTabActiveState(root.querySelector('[data-bs-toggle="tab"].active') || tabControls[0]);
		updateStatusFromPane(activePane());
		activateFromHash();
		window.addEventListener('hashchange', activateFromHash);
	});
});
