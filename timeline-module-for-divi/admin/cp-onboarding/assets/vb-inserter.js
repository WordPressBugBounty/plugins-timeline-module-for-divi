( function () {
	'use strict';

	if ( new URLSearchParams( window.location.search ).get( 'tmdivi_onboarding' ) !== '1' ) {
		return;
	}

	var done = false;
	var started = false;
	var attempts = 0;
	var timer = null;

	function hasTimeline() {
		return !! document.querySelector(
			'[class*="tmdivi_timeline"], .tmdivi-wrapper, [data-module-type="tmdivi/timeline"]'
		);
	}

	function finish() {
		done = true;
		if ( timer ) {
			clearInterval( timer );
			timer = null;
		}
		var url = new URL( window.location.href );
		if ( url.searchParams.has( 'tmdivi_onboarding' ) ) {
			url.searchParams.delete( 'tmdivi_onboarding' );
			window.history.replaceState( {}, '', url.toString() );
		}
	}

	function clickTimeline() {
		var btn = document.querySelector( 'button[value="tmdivi/timeline"]' );
		if ( btn && ! btn.disabled ) {
			btn.click();
			return true;
		}
		return false;
	}

	function openInserter() {
		// Only empty-column add buttons — not hover controls on existing modules.
		var columns = document.querySelectorAll( '.et-vb-column, .et_pb_column' );
		for ( var i = 0; i < columns.length; i++ ) {
			var col = columns[i];
			if ( col.querySelector( '[class*="tmdivi_timeline"], .tmdivi-wrapper' ) ) {
				continue;
			}
			var el = col.querySelector( '.et-vb-add-module, .et-vb-icon--add' );
			if ( el && ! el.disabled ) {
				el.click();
				return true;
			}
		}
		return false;
	}

	function tick() {
		if ( done || attempts++ > 150 ) {
			finish();
			return true;
		}
		if ( hasTimeline() ) {
			finish();
			return true;
		}
		if ( clickTimeline() ) {
			finish();
			return true;
		}
		if ( attempts === 1 || attempts % 5 === 0 ) {
			openInserter();
		}
		return false;
	}

	function start() {
		if ( started || done ) {
			return;
		}
		started = true;

		if ( hasTimeline() ) {
			finish();
			return;
		}

		if ( tick() ) {
			return;
		}
		timer = setInterval( function () {
			if ( tick() ) {
				clearInterval( timer );
				timer = null;
			}
		}, 200 );
	}

	window.addEventListener( 'et_builder_api_ready', start );
	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', start );
	} else {
		start();
	}
} )();
