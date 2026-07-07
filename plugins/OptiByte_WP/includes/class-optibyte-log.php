<?php
/**
 * OptiByte WP — optimization log.
 */

defined( 'ABSPATH' ) || exit;

class OptiByte_Log {

	public static function load() {
		$file = OptiByte_Config::log_file();
		if ( ! file_exists( $file ) ) {
			return array();
		}
		$data = json_decode( (string) file_get_contents( $file ), true );
		return is_array( $data ) ? $data : array();
	}

	public static function add( array $entry ) {
		$log   = self::load();
		$log[] = array_merge(
			array( 'logged_at' => gmdate( 'c' ) ),
			$entry
		);
		$max = 500;
		if ( count( $log ) > $max ) {
			$log = array_slice( $log, -$max );
		}
		file_put_contents(
			OptiByte_Config::log_file(),
			wp_json_encode( $log, JSON_PRETTY_PRINT ),
			LOCK_EX
		);
	}

	public static function clear() {
		file_put_contents( OptiByte_Config::log_file(), '[]', LOCK_EX );
	}
}
