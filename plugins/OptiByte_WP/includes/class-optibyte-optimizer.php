<?php
/**
 * OptiByte WP — process pending queue jobs (WP-Cron or manual).
 */

defined( 'ABSPATH' ) || exit;

class OptiByte_Optimizer {

	/**
	 * @return int Success count.
	 */
	public static function process_queue() {
		$cfg        = OptiByte_Config::get();
		$output_dir = OptiByte_Config::output_dir();
		$queue      = OptiByte_Queue::load();
		$updated    = false;
		$success    = 0;

		foreach ( $queue as &$job ) {
			if ( ( $job['status'] ?? '' ) !== 'pending' ) {
				continue;
			}

			$source = $job['path'] ?? '';
			if ( ! $source || ! file_exists( $source ) ) {
				$job['status'] = 'error';
				$job['error']  = 'Missing source file';
				$updated       = true;
				continue;
			}

			$style    = $job['style'] ?? $cfg['default_style'];
			$filename = pathinfo( $source, PATHINFO_FILENAME );
			$start    = microtime( true );

			$job['status']  = 'processing';
			$job['started'] = gmdate( 'c' );
			$updated        = true;

			$work_source = $source;
			$styled_tmp  = $output_dir . $filename . '-styled.png';

			if ( OptiByte_Ai_Client::is_ai_style( $style ) && OptiByte_Ai_Client::configured() ) {
				$ai = OptiByte_Ai_Client::process_file( $source, $style, $cfg['formats'] );
				if ( $ai['ok'] && ! empty( $ai['path'] ) ) {
					$job['status']     = 'done';
					$job['completed']  = gmdate( 'c' );
					$job['duration_ms'] = (int) round( ( microtime( true ) - $start ) * 1000 );
					$job['outputs']    = array( 'ai' => $ai['path'] );
					OptiByte_Log::add(
						array(
							'file'         => basename( $source ),
							'status'       => 'done',
							'style'        => $style,
							'engine'       => 'api',
							'duration_ms'  => $job['duration_ms'],
							'started'      => $job['started'],
							'completed'    => $job['completed'],
						)
					);
					$success++;
					continue;
				}
				// Fall back to local Imagik presets when API unavailable.
			}

			if ( 'none' !== $style ) {
				OptiByte_Imagik::apply_local_style( $source, $styled_tmp, $style );
				if ( file_exists( $styled_tmp ) ) {
					$work_source = $styled_tmp;
				}
			}

			$outputs = array();
			foreach ( $cfg['formats'] as $format ) {
				$quality = 'avif' === $format ? (int) $cfg['avif_quality'] : (int) $cfg['webp_quality'];
				$out     = $output_dir . $filename . '.' . $format;
				if ( OptiByte_Imagik::encode( $work_source, $out, $format, $quality ) ) {
					$outputs[ $format ] = $out;
				}
			}

			if ( file_exists( $styled_tmp ) ) {
				@unlink( $styled_tmp );
			}

			if ( count( $outputs ) === count( $cfg['formats'] ) ) {
				$duration           = (int) round( ( microtime( true ) - $start ) * 1000 );
				$job['status']      = 'done';
				$job['completed']   = gmdate( 'c' );
				$job['duration_ms'] = $duration;
				$job['outputs']     = $outputs;

				OptiByte_Log::add(
					array(
						'file'        => basename( $source ),
						'status'      => 'done',
						'style'       => $style,
						'engine'      => OptiByte_Imagik::engine_label(),
						'webp_size'   => isset( $outputs['webp'] ) ? filesize( $outputs['webp'] ) : 0,
						'avif_size'   => isset( $outputs['avif'] ) ? filesize( $outputs['avif'] ) : 0,
						'duration_ms' => $duration,
						'started'     => $job['started'],
						'completed'   => $job['completed'],
					)
				);
				$success++;
			} else {
				$job['status'] = 'error';
				$job['error']  = 'Imagik encode failed';
				OptiByte_Log::add(
					array(
						'file'      => basename( $source ),
						'status'    => 'error',
						'message'   => $job['error'],
						'style'     => $style,
						'engine'    => OptiByte_Imagik::engine_label(),
						'started'   => $job['started'],
						'completed' => gmdate( 'c' ),
					)
				);
			}
		}
		unset( $job );

		if ( $updated ) {
			OptiByte_Queue::save( $queue );
		}

		return $success;
	}
}
