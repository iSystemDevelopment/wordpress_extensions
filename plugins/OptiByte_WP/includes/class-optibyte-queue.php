<?php
/**
 * OptiByte WP — JSON job queue under wp-content/uploads.
 */

defined( 'ABSPATH' ) || exit;

class OptiByte_Queue {

	public static function load() {
		$file = OptiByte_Config::queue_file();
		if ( ! file_exists( $file ) ) {
			return array();
		}
		$data = json_decode( (string) file_get_contents( $file ), true );
		return is_array( $data ) ? $data : array();
	}

	public static function save( array $queue ) {
		file_put_contents(
			OptiByte_Config::queue_file(),
			wp_json_encode( $queue, JSON_PRETTY_PRINT ),
			LOCK_EX
		);
	}

	public static function add( array $job ) {
		$queue   = self::load();
		$queue[] = $job;
		self::save( $queue );
	}

	public static function clear() {
		self::save( array() );
	}

	/** @return resource|false */
	public static function lock() {
		$lock = fopen( OptiByte_Config::lock_file(), 'c+' );
		if ( ! $lock || ! flock( $lock, LOCK_EX | LOCK_NB ) ) {
			return false;
		}
		return $lock;
	}

	/** @param resource|false $lock */
	public static function unlock( $lock ) {
		if ( $lock ) {
			flock( $lock, LOCK_UN );
			fclose( $lock );
		}
	}
}
