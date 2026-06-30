/**
 * Gallery lightbox — Fancybox bundled via Vite.
 */
import { Fancybox } from '@fancyapps/ui';
import '@fancyapps/ui/dist/fancybox.css';

document.addEventListener('DOMContentLoaded', function () {
	Fancybox.bind('[data-fancybox="gallery"]', {
		Toolbar: false,
		animated: false,
		dragToClose: false,
		showClass: false,
		hideClass: false,
		closeButton: 'top',
		Image: {
			click: 'close',
			wheel: 'slide',
			zoom: false,
			fit: 'cover',
		},
		Thumbs: {
			minScreenHeight: 0,
		},
	});
});
