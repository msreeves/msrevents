import { defineConfig } from 'vite';
import { resolve } from 'path';

export default defineConfig({
	// Relative asset URLs so fonts resolve under …/themes/msrevents/dist/ on multisite.
	base: './',
	build: {
		outDir: 'dist',
		// Keep existing files in dist/ that Vite doesn't own (e.g. source maps from prior builds)
		emptyOutDir: false,
		sourcemap: true,
		rollupOptions: {
			input: resolve(import.meta.dirname, 'src/js/app.js'),
			output: {
				// Fixed filenames — no content hash — so script-styles.php filemtime versioning
				// continues to work without any PHP changes.
				entryFileNames: 'app.js',
				chunkFileNames: 'app-[name].js',
				assetFileNames: (assetInfo) => {
					if (assetInfo.name?.endsWith('.css')) return 'app.css';
					return '[name][extname]';
				},
			},
		},
	},
	css: {
		preprocessorOptions: {
			scss: {
				// Modern Sass JS API — eliminates the legacy-js-api deprecation warning.
				api: 'modern',
				// Allows @import 'bootstrap/scss/...' and @import 'include-media/...'
				// without the node_modules/ prefix.
				loadPaths: [resolve(import.meta.dirname, 'node_modules')],
				// Bootstrap 5.3 and include-media still use @import / global builtins.
				// Silence their deprecations until upstream migrates to @use.
				silenceDeprecations: [
					'import',
					'if-function',
					'global-builtin',
					'color-functions',
					'abs-percent',
				],
			},
		},
	},
});
