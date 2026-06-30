/**
 * Key-point stat counters — vanilla rAF (replaces jQuery animation.js).
 */
document.addEventListener('DOMContentLoaded', function () {
	var counters = document.querySelectorAll('.count');
	if (!counters.length) {
		return;
	}

	if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
		return;
	}

	var io = new IntersectionObserver(
		function (entries) {
			entries.forEach(function (entry) {
				if (!entry.isIntersecting) {
					return;
				}
				var el = entry.target;
				io.unobserve(el);
				var target = parseInt(el.textContent, 10);
				if (Number.isNaN(target)) {
					return;
				}
				var start = performance.now();
				var duration = 1000;
				function tick(now) {
					var p = Math.min((now - start) / duration, 1);
					var eased = 1 - Math.pow(1 - p, 3);
					el.textContent = String(Math.ceil(target * eased));
					if (p < 1) {
						requestAnimationFrame(tick);
					}
				}
				requestAnimationFrame(tick);
			});
		},
		{ threshold: 0.3 }
	);

	counters.forEach(function (el) {
		io.observe(el);
	});
});
