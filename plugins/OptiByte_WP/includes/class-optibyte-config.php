<?php
/**
 * OptiByte WP — paths and settings (wp_options SSOT).
 */

defined( 'ABSPATH' ) || exit;

class OptiByte_Config {

	const OPTION_KEY = 'optibyte_wp_settings';

	public static function defaults() {
		return array(
			'webp_quality'      => 85,
			'avif_quality'      => 60,
			'formats'           => array( 'webp', 'avif' ),
			'default_style'     => 'none',
			'api_base'          => 'https://api.isystem.app',
			'api_service_token' => '',
			'auto_scan_uploads' => false,
		);
	}

	public static function get() {
		$stored = get_option( self::OPTION_KEY, array() );
		return wp_parse_args( is_array( $stored ) ? $stored : array(), self::defaults() );
	}

	public static function update( array $patch ) {
		$next = array_merge( self::get(), $patch );
		update_option( self::OPTION_KEY, $next, false );
		return $next;
	}

	public static function base_dir() {
		$upload = wp_upload_dir();
		$dir    = trailingslashit( $upload['basedir'] ) . 'optibyte-wp';
		if ( ! is_dir( $dir ) ) {
			wp_mkdir_p( $dir );
		}
		return trailingslashit( $dir );
	}

	public static function staging_dir() {
		$dir = self::base_dir() . 'staging/';
		if ( ! is_dir( $dir ) ) {
			wp_mkdir_p( $dir );
		}
		return $dir;
	}

	public static function output_dir() {
		$dir = self::base_dir() . 'optimized/';
		if ( ! is_dir( $dir ) ) {
			wp_mkdir_p( $dir );
		}
		return $dir;
	}

	public static function queue_file() {
		return self::base_dir() . 'queue.json';
	}

	public static function log_file() {
		return self::base_dir() . 'log.json';
	}

	public static function lock_file() {
		return self::base_dir() . 'queue.lock';
	}

	/** @return string[] */
	public static function ai_styles() {
		return array( 'none', 'enhance', 'cartoon', 'artistic', 'vintage', 'abstract' );
	}
}
