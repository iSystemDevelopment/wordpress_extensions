<?php
/**
 * OptiByte WP — scan uploads/staging into the job queue.
 */

defined( 'ABSPATH' ) || exit;

class OptiByte_Scanner {

	/**
	 * @param string|null $dir Override scan directory.
	 * @return int Jobs added.
	 */
	public static function scan( $dir = null ) {
		$image_dir = $dir ? trailingslashit( $dir ) : OptiByte_Config::staging_dir();
		if ( ! is_dir( $image_dir ) ) {
			wp_mkdir_p( $image_dir );
		}

		$patterns = array( '*.jpg', '*.jpeg', '*.png', '*.gif', '*.webp' );
		$images   = array();
		foreach ( $patterns as $pattern ) {
			$found = glob( $image_dir . $pattern, GLOB_NOSORT );
			if ( $found ) {
				$images = array_merge( $images, $found );
			}
		}

		if ( ! $images ) {
			return 0;
		}

		$lock = OptiByte_Queue::lock();
		if ( ! $lock ) {
			return 0;
		}

		$queue    = OptiByte_Queue::load();
		$existing = array_column( $queue, 'file' );
		$added    = 0;

		foreach ( $images as $img_path ) {
			$basename = basename( $img_path );
			if ( in_array( $basename, $existing, true ) ) {
				continue;
			}
			$queue[] = array(
				'file'    => $basename,
				'path'    => $img_path,
				'status'  => 'pending',
				'style'   => OptiByte_Config::get()['default_style'],
				'created' => gmdate( 'c' ),
			);
			$added++;
		}

		OptiByte_Queue::save( $queue );
		OptiByte_Queue::unlock( $lock );

		return $added;
	}

	/**
	 * Queue a single WordPress attachment for optimization.
	 *
	 * @param int $attachment_id
	 */
	public static function queue_attachment( $attachment_id ) {
		$path = get_attached_file( $attachment_id );
		if ( ! $path || ! file_exists( $path ) ) {
			return false;
		}
		$mime = get_post_mime_type( $attachment_id );
		if ( ! $mime || strpos( $mime, 'image/' ) !== 0 ) {
			return false;
		}

		OptiByte_Queue::add(
			array(
				'file'          => basename( $path ),
				'path'          => $path,
				'attachment_id' => (int) $attachment_id,
				'status'        => 'pending',
				'style'         => OptiByte_Config::get()['default_style'],
				'created'       => gmdate( 'c' ),
			)
		);
		return true;
	}
}
