<?php
/**
 * Plugin Name:       iSystem GCC Plus (Astra + Spectra Hardening)
 * Plugin URI:        https://isystem.app/
 * Description:       Admin/editor hardening for Astra + Spectra: protect critical Gutenberg scripts from async/defer, trim heavy admin assets/widgets, remove front-end jQuery Migrate, quiet Spectra duplicate-store warnings. No Jetpack controls included.
 * Version:           2.4.0
 * Requires at least: 6.3
 * Requires PHP:      7.4
 * Author:            iSystem Development
 * Author URI:        https://isystem.app/
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       isystem-gccplus
 *
 * Copyright (c) 2026 iSystem Development / DIODAC ELECTRONICS
 * Also available under MIT for non-.org distributions.
 */

defined( 'ABSPATH' ) || exit;

define( 'ISYSTEM_GCC_PLUS_VERSION', '2.4.0' );

/**
 * 1) Front end: remove jQuery Migrate (leave /wp-admin/ alone).
 */
add_filter(
	'wp_default_scripts',
	static function ( $scripts ) {
		if ( is_admin() || ! isset( $scripts->registered['jquery'] ) ) {
			return;
		}
		$scripts->registered['jquery']->deps = array_diff(
			$scripts->registered['jquery']->deps,
			array( 'jquery-migrate' )
		);
	}
);

/**
 * 2) Admin: strip async/defer from critical editor packages (load-order safety).
 */
add_filter(
	'script_loader_tag',
	static function ( $tag, $handle ) {
		if ( ! is_admin() ) {
			return $tag;
		}
		$critical = array(
			'react',
			'react-dom',
			'react-jsx-runtime',
			'wp-hooks',
			'wp-i18n',
			'wp-element',
			'wp-components',
			'wp-compose',
			'wp-data',
			'wp-data-controls',
			'wp-url',
			'wp-api-fetch',
			'wp-block-editor',
			'wp-editor',
			'wp-plugins',
			'wp-primitives',
			'wp-deprecated',
			'wp-keycodes',
			'wp-warning',
		);
		if ( in_array( $handle, $critical, true ) ) {
			$tag = preg_replace( '/\sdefer(=("|\'?)[^"\']*\2)?/i', '', $tag );
			$tag = preg_replace( '/\sasync(=("|\'?)[^"\']*\2)?/i', '', $tag );
		}
		return $tag;
	},
	20,
	2
);

/**
 * 3) Admin: dequeue known heavy extras matched by stable URL fragments.
 */
function isystem_gcc_dequeue_admin_perf_hogs() {
	if ( ! is_admin() ) {
		return;
	}
	global $wp_scripts, $wp_styles;

	$matchers_js = array(
		'ultimate-addons-for-gutenberg/lib/zip-ai/sidebar/build/sidebar-app.js',
		'ultimate-addons-for-gutenberg/lib/zipwp-images/dist/main.js',
		'object-cache-pro/resources/vendor/apexcharts',
		'object-cache-pro/resources/js/metrics.js',
		'wp-mail-smtp-pro/assets/js/vendor/chart.min.js',
		'query-monitor/assets/query-monitor.js',
		'static.cloudflareinsights.com/beacon.min.js',
	);

	$matchers_css = array(
		'ultimate-addons-for-gutenberg/lib/zip-ai/sidebar/build/sidebar-app.css',
		'wp-mail-smtp-pro/assets/css/dashboard-widget.min.css',
	);

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
				if ( false !== strpos( $src, $needle ) ) {
					if ( 'script' === $type ) {
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
	$maybe_remove( $wp_styles, 'style', $matchers_css );
}
add_action( 'admin_enqueue_scripts', 'isystem_gcc_dequeue_admin_perf_hogs', 999 );

/**
 * 4) Dashboard: drop heavy / low-value widgets.
 */
add_action(
	'wp_dashboard_setup',
	static function () {
		remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
		remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
		remove_meta_box( 'dashboard_activity', 'dashboard', 'normal' );
		remove_meta_box( 'dashboard_site_health', 'dashboard', 'normal' );
		remove_meta_box( 'dashboard_objectcache', 'dashboard', 'normal' );
		remove_meta_box( 'wp_mail_smtp_reports_widget', 'dashboard', 'normal' );
		remove_meta_box( 'wp_mail_smtp_reports_widget', 'dashboard', 'side' );
	},
	99
);

/**
 * 5) Spectra/UAG: quiet duplicate store registration warnings.
 */
add_action(
	'admin_enqueue_scripts',
	static function () {
		if ( ! wp_script_is( 'wp-data', 'enqueued' ) && ! wp_script_is( 'wp-data', 'registered' ) ) {
			return;
		}

		$js = <<<'JS'
(function() {
	if ( !window.wp || !wp.data || typeof wp.data.registerStore !== 'function' ) return;
	var _registerStore = wp.data.registerStore;
	wp.data.registerStore = function(name, store) {
		try {
			if ( name && wp.data.stores && wp.data.stores[name] ) {
				return wp.data.stores[name];
			}
		} catch (e) {}
		return _registerStore.apply(this, arguments);
	};
})();
JS;
		wp_add_inline_script( 'wp-data', $js, 'after' );
	},
	1
);

/**
 * 6) Admin: ReactModal app-element fallback (quiets a11y console noise).
 */
add_action(
	'admin_print_footer_scripts',
	static function () {
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
		wp_register_script( 'isystem-gcc-reactmodal', false, array(), ISYSTEM_GCC_PLUS_VERSION, true );
		wp_enqueue_script( 'isystem-gcc-reactmodal' );
		wp_add_inline_script( 'isystem-gcc-reactmodal', $js );
	},
	20
);

/**
 * 7) Astra Customizer: ensure pagination typography keys exist.
 */
add_filter(
	'option_astra-settings',
	static function ( $value ) {
		if ( ! is_array( $value ) ) {
			$value = array();
		}
		$key      = 'blog-content-pagination-typo';
		$defaults = array(
			'font-family' => 'inherit',
			'font-weight' => '400',
		);
		$value[ $key ] = ( isset( $value[ $key ] ) && is_array( $value[ $key ] ) )
			? array_merge( $defaults, $value[ $key ] )
			: $defaults;

		return $value;
	},
	20
);
