/**
 * Hero / section reveal — CSS transitions + IntersectionObserver (replaces Animate.css).
 */
document.documentElement.classList.add('js-reveal');

function msreventsRevealNodes(root) {
	var scope = root && root.querySelectorAll ? root : document;
	var els = scope.querySelectorAll
		? scope.querySelectorAll('.msr-reveal:not(.is-visible)')
		: [];

	if (!els.length) {
		return;
	}

	if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
		els.forEach(function (el) {
			el.classList.add('is-visible');
		});
		return;
	}

	function reveal(el) {
		el.classList.add('is-visible');
	}

	function inView(el) {
		var rect = el.getBoundingClientRect();
		return rect.top < window.innerHeight * 0.92 && rect.bottom > 0;
	}

	var io = new IntersectionObserver(
		function (entries) {
			entries.forEach(function (entry) {
				if (entry.isIntersecting) {
					reveal(entry.target);
					io.unobserve(entry.target);
				}
			});
		},
		{ threshold: 0.12, rootMargin: '0px 0px -4% 0px' }
	);

	els.forEach(function (el) {
		if (inView(el)) {
			reveal(el);
		} else {
			io.observe(el);
		}
	});
}

window.msrreventsRevealNodes = msreventsRevealNodes;

document.addEventListener('DOMContentLoaded', function () {
	msreventsRevealNodes(document);
});
