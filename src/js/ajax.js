/**
 * Load more: fetch + FormData (matches admin-ajax loadmore handler).
 */
document.addEventListener('DOMContentLoaded', function() {
	document.querySelectorAll('.btn-load-more').forEach(function(button) {
		button.addEventListener('click', function(e) {
			e.preventDefault();

			var params = window.loadmore_params;
			if (button.disabled || !params || !params.ajaxurl) {
				return;
			}

			var section = button.closest('[data-loadmore-max-pages], .msrevents-stories-list, section');
			var cfg = section
				? {
						limit: parseInt(section.getAttribute('data-loadmore-limit') || '3', 10),
						page: parseInt(section.getAttribute('data-loadmore-page') || '1', 10),
						maxPages: parseInt(section.getAttribute('data-loadmore-max-pages') || '1', 10),
						listingType: section.getAttribute('data-loadmore-listing-type') || 'latest',
						termId: parseInt(section.getAttribute('data-loadmore-term-id') || '0', 10),
						category: section.getAttribute('data-loadmore-category') || '',
					}
				: window.msrLoadmoreConfig;

			if (!cfg || !cfg.maxPages || cfg.page >= cfg.maxPages) {
				return;
			}

			button.disabled = true;
			button.setAttribute('aria-busy', 'true');
			var limit = cfg.limit || 3;
			var page = cfg.page || 1;
			var maxPages = cfg.maxPages || 1;
			var listingType = cfg.listingType || 'latest';
			var termId = cfg.termId || 0;
			var category = cfg.category || '';
			var loadWrap = button.closest('.load_more');
			var statusEl = loadWrap && loadWrap.querySelector('.msr-load-more-status');

			var fd = new FormData();
			fd.append('action', 'loadmore');
			fd.append('nonce', params.nonce || '');
			fd.append('limit', String(limit));
			fd.append('page', String(page));
			fd.append('listing_type', listingType);
			fd.append('term_id', String(termId));
			fd.append('category', category);

			var idle = button.getAttribute('data-idle-text') || button.textContent || 'Load more';
			var emptyText = button.getAttribute('data-empty-text') || 'No more stories';
			button.textContent = button.getAttribute('data-loading-text') || 'Loading…';
			if (statusEl) {
				statusEl.textContent = button.textContent;
				statusEl.classList.remove('screen-reader-text');
			}

			fetch(params.ajaxurl, {
				method: 'POST',
				credentials: 'same-origin',
				body: fd,
			})
				.then(function(res) {
					return res.text();
				})
				.then(function(html) {
					var body = String(html || '').trim();
					var hasPosts = body.indexOf('post-card') !== -1;

					if (hasPosts) {
						var wrapSection = button.closest('.msrevents-stories-list, section');
						var wrap = wrapSection
							? wrapSection.querySelector('.latest_posts_wrapper')
							: document.querySelector('.latest_posts_wrapper');
						if (wrap) {
							wrap.insertAdjacentHTML('beforeend', body);
							if (typeof window.msrreventsRevealNodes === 'function') {
								window.msrreventsRevealNodes(wrap);
							}
						}
						cfg.page = page + 1;
						if (section) {
							section.setAttribute('data-loadmore-page', String(cfg.page));
						}
						if (cfg.page >= maxPages) {
							var loadWrapDone = button.closest('.load_more');
							if (loadWrapDone) {
								loadWrapDone.innerHTML =
									'<p class="msr-load-more-status text-center" role="status">' + emptyText + '</p>';
							}
							return;
						}
						button.textContent = idle;
						if (statusEl) {
							statusEl.textContent = '';
							statusEl.classList.add('screen-reader-text');
						}
					} else {
						var loadWrapEmpty = button.closest('.load_more');
						if (loadWrapEmpty) {
							loadWrapEmpty.innerHTML =
								'<p class="msr-load-more-status text-center" role="status">' + emptyText + '</p>';
						}
					}
				})
				.catch(function() {
					button.textContent = button.getAttribute('data-error-text') || 'Retry';
					if (statusEl) {
						statusEl.textContent = button.textContent;
						statusEl.classList.remove('screen-reader-text');
					}
				})
				.finally(function() {
					if (button.isConnected) {
						button.disabled = false;
						button.removeAttribute('aria-busy');
					}
				});
		});
	});
});
