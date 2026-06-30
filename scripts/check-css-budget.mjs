#!/usr/bin/env node
/**
 * MSR Events hub CSS budget gate (Phase 19).
 *
 * Usage: node scripts/check-css-budget.mjs
 * Env: MSR_EVENTS_CSS_BUDGET_BYTES (default 360448)
 */
import { readFileSync, statSync } from 'node:fs';
import { resolve, dirname } from 'node:path';
import { fileURLToPath } from 'node:url';

const __dirname = dirname( fileURLToPath( import.meta.url ) );
const cssPath = resolve( __dirname, '../dist/app.css' );
const maxBytes = Number( process.env.MSR_EVENTS_CSS_BUDGET_BYTES || 360448 );

let size;
try {
	size = statSync( cssPath ).size;
} catch {
	console.error( `check-css-budget: missing ${cssPath} — run npm run production first` );
	process.exit( 1 );
}

const kb = ( size / 1024 ).toFixed( 1 );
const maxKb = ( maxBytes / 1024 ).toFixed( 1 );

if ( size > maxBytes ) {
	console.error( `check-css-budget: FAIL dist/app.css ${kb} KiB exceeds budget ${maxKb} KiB` );
	process.exit( 1 );
}

const css = readFileSync( cssPath, 'utf8' );
if ( /fonts\.googleapis\.com|cdn\.jsdelivr\.net|kit\.fontawesome\.com/.test( css ) ) {
	console.error( 'check-css-budget: FAIL dist/app.css still references external CDN hosts' );
	process.exit( 1 );
}

console.log( `check-css-budget: PASS dist/app.css ${kb} KiB (budget ${maxKb} KiB)` );
process.exit( 0 );
