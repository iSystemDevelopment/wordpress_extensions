<?php
/**
 * Plugin Name: iSystem GCC Plus (Astra + Spectra Hardening)
 * Description: Fixes Spectra duplicate-store warnings, protects critical editor scripts from async/defer, trims heavy admin assets/widgets, and keeps jQuery Migrate off the front end. No Jetpack tweaks included.
 * Author: iSystem Developments
 * Author URI: https://www.isystem.app
 * Version: 2.3.0
 * Requires at least: 6.3
 * Requires PHP: 7.4
 * License: MIT
 * Text Domain: isystem-gccplus
 */

defined( 'ABSPATH' ) || exit;

/**
 * 1) Front end: remove jQuery Migrate (leave /wp-admin/ alone for safety).
 */
add_filter( 'wp_default_scripts', function ( $scripts ) {
	if ( is_admin() || ! isset( $scripts->registered['jquery'] ) ) {
		return;
	}
	$scripts->registered['jquery']->deps = array_diff(
		$scripts->registered['jquery']->deps,
		[ 'jquery-migrate' ]
	);
} );

/**
 * 2) Admin: protect critical editor packages from async/defer/rocket-loader.
 *    This guards load order issues that can cascade into Spectra/Gutenberg bugs.
 */
add_filter( 'script_loader_tag', function ( $tag, $handle ) {
	if ( ! is_admin() ) {
		return $tag;
	}
	$critical = [
		'react', 'react-dom', 'react-jsx-runtime',
		'wp-hooks', 'wp-i18n', 'wp-element', 'wp-components', 'wp-compose',
		'wp-data', 'wp-data-controls', 'wp-url', 'wp-api-fetch',
		'wp-block-editor', 'wp-editor', 'wp-plugins', 'wp-primitives',
		'wp-deprecated', 'wp-keycodes', 'wp-warning',
	];
	if ( in_array( $handle, $critical, true ) ) {
		$tag = preg_replace( '/\sdefer(=("|\'?)[^"\']*\2)?/i', '', $tag );
		$tag = preg_replace( '/\sasync(=("|\'?)[^"\']*\2)?/i', '', $tag );
	}
	return $tag;
}, 20, 2 );

/**
 * 3) Admin: dequeue known heavy dashboard/editor extras.
 *    Matches by URL fragment (stable even if handles change).
 */
function isystem_gcc_dequeue_admin_perf_hogs() {
	if ( ! is_admin() ) {
		return;
	}
	global $wp_scripts, $wp_styles;

	$matchers_js = [
		// Spectra/UAG extras
		'ultimate-addons-for-gutenberg/lib/zip-ai/sidebar/build/sidebar-app.js',
		'ultimate-addons-for-gutenberg/lib/zipwp-images/dist/main.js',
		// Object Cache Pro charts
		'object-cache-pro/resources/vendor/apexcharts',
		'object-cache-pro/resources/js/metrics.js',
		// WP Mail SMTP chart
		'wp-mail-smtp-pro/assets/js/vendor/chart.min.js',
		// Query Monitor UI
		'query-monitor/assets/query-monitor.js',
		// Cloudflare beacon (no value in wp-admin)
		'static.cloudflareinsights.com/beacon.min.js',
	];

	$matchers_css = [
		'ultimate-addons-for-gutenberg/lib/zip-ai/sidebar/build/sidebar-app.css',
		'wp-mail-smtp-pro/assets/css/dashboard-widget.min.css',
	];

	$maybe_remove = static function ( $registry, $type, $needles ) {
		if ( ! $registry || empty( $registry->registered ) ) {
			return;
		}
		foreach ( $registry->registered as $handle => $obj ) {
			$src = isset( $obj->src ) ? (string) $obj->src : '';
			if ( ! $src ) {
				continue;
			}
			foreach ( $needles as $needle ) {
				if ( strpos( $src, $needle ) !== false ) {
					if ( $type === 'script' ) {
						wp_dequeue_script( $handle );
						wp_deregister_script( $handle );
					} else {
						wp_dequeue_style( $handle );
						wp_deregister_style( $handle );
					}
					break;
				}
			}
		}
	};

	$maybe_remove( $wp_scripts, 'script', $matchers_js );
	$maybe_remove( $wp_styles,  'style',  $matchers_css );
}
add_action( 'admin_enqueue_scripts', 'isystem_gcc_dequeue_admin_perf_hogs', 999 );

/**
 * 4) Admin: trim heavy dashboard widgets to reduce blocking time on /wp-admin/.
 */
add_action( 'wp_dashboard_setup', function () {
	// Optional core widgets.
	remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
	remove_meta_box( 'dashboard_primary',     'dashboard', 'side' );
	remove_meta_box( 'dashboard_activity',    'dashboard', 'normal' );
	remove_meta_box( 'dashboard_site_health', 'dashboard', 'normal' );
	// Third‑party widgets seen in audits.
	remove_meta_box( 'dashboard_objectcache',       'dashboard', 'normal' ); // Object Cache Pro.
	remove_meta_box( 'wp_mail_smtp_reports_widget', 'dashboard', 'normal' ); // WP Mail SMTP.
	remove_meta_box( 'wp_mail_smtp_reports_widget', 'dashboard', 'side' );
}, 99 );

/**
 * 5) Spectra/UAG: guard duplicate store registration.
 *    Prevents “Store 'spectra' is already registered.” from noisy re-registers.
 *
 *    We inject AFTER wp-data so the guard exists before plugins add their stores.
 */
add_action( 'admin_enqueue_scripts', function () {
	if ( ! wp_script_is( 'wp-data', 'enqueued' ) ) {
		// Core enqueues wp-data on most admin screens; if it isn’t, nothing to do.
		return;
	}

	$js = <<<'JS'
(function() {
	if ( !window.wp || !wp.data || typeof wp.data.registerStore !== 'function' ) return;
	var _registerStore = wp.data.registerStore;
	wp.data.registerStore = function(name, store) {
		try {
			// If the store already exists, return it quietly instead of warning.
			if ( name && wp.data.stores && wp.data.stores[name] ) {
				return wp.data.stores[name];
			}
		} catch (e) {}
		return _registerStore.apply(this, arguments);
	};
})();
JS;
	// Run right after wp-data so anything enqueued later sees the patched method.
	wp_add_inline_script( 'wp-data', $js, 'after' );
}, 1 );

/**
 * 6) Admin: ReactModal fallback (quiets “getApplicationElement” style warnings).
 */
add_action( 'admin_print_footer_scripts', function () {
	$js = <<<'JS'
(function(){
	try {
		var root = document.getElementById('wpwrap') || document.getElementById('wpbody-content') || document.body;
		if ( window.ReactModal && typeof window.ReactModal.setAppElement === 'function' ) {
			window.ReactModal.setAppElement(root);
		}
		if ( window.ReactModal2 && typeof window.ReactModal2.setAppElement === 'function' ) {
			window.ReactModal2.setAppElement(root);
		}
	} catch(e) {}
})();
JS;
	wp_register_script( 'isystem-gcc-reactmodal', '', [], null, true );
	wp_enqueue_script( 'isystem-gcc-reactmodal' );
	wp_add_inline_script( 'isystem-gcc-reactmodal', $js );
}, 20 );

/**
 * 7) Astra Customizer: ensure missing typography keys exist to quiet notices.
 */
add_filter( 'option_astra-settings', function( $value ) {
	if ( ! is_array( $value ) ) {
		$value = [];
	}
	$key      = 'blog-content-pagination-typo';
	$defaults = [ 'font-family' => 'inherit', 'font-weight' => '400' ];
	$value[ $key ] = isset( $value[ $key ] ) && is_array( $value[ $key ] )
		? array_merge( $defaults, $value[ $key ] )
		: $defaults;

	return $value;
}, 20 );