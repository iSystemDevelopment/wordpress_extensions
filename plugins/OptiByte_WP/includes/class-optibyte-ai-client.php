<?php
/**
 * OptiByte WP — AI style client (iSystem API / OptiByte engine).
 */

defined( 'ABSPATH' ) || exit;

class OptiByte_Ai_Client {

	const AI_STYLES = array( 'enhance', 'cartoon', 'artistic', 'vintage', 'abstract' );

	/** @return bool */
	public static function is_ai_style( $style ) {
		return in_array( strtolower( (string) $style ), self::AI_STYLES, true );
	}

	/** @return bool */
	public static function configured() {
		$cfg = OptiByte_Config::get();
		return ! empty( $cfg['api_service_token'] );
	}

	/**
	 * Request AI-processed image bytes from api.isystem.app.
	 *
	 * @return array{ok:bool,path?:string,error?:string}
	 */
	public static function process_file( $source_path, $style, $formats = array( 'webp' ) ) {
		if ( ! self::configured() ) {
			return array(
				'ok'    => false,
				'error' => 'API service token not configured (see STUBS.md)',
			);
		}

		if ( ! file_exists( $source_path ) || ! is_readable( $source_path ) ) {
			return array( 'ok' => false, 'error' => 'Source file not readable' );
		}

		$cfg  = OptiByte_Config::get();
		$url  = trailingslashit( $cfg['api_base'] ) . 'api/v1/optibyte/images/process';
		$mime = wp_check_filetype( $source_path );
		$body = array(
			'files'   => array(
				array(
					'name' => basename( $source_path ),
					'type' => $mime['type'] ?: 'image/jpeg',
					'bits' => file_get_contents( $source_path ),
				),
			),
			'formats' => implode( ',', $formats ),
			'sizes'   => 'original',
			'quality' => (string) $cfg['webp_quality'],
			'style'   => $style,
		);

		$boundary = wp_generate_password( 24, false );
		$payload  = self::build_multipart( $body, $boundary );

		$response = wp_remote_post(
			$url,
			array(
				'timeout' => 120,
				'headers' => array(
					'Authorization' => 'Bearer ' . $cfg['api_service_token'],
					'Content-Type'    => 'multipart/form-data; boundary=' . $boundary,
				),
				'body'    => $payload,
			)
		);

		if ( is_wp_error( $response ) ) {
			return array( 'ok' => false, 'error' => $response->get_error_message() );
		}

		$code = wp_remote_retrieve_response_code( $response );
		$json = json_decode( (string) wp_remote_retrieve_body( $response ), true );

		if ( $code < 200 || $code >= 300 ) {
			$msg = is_array( $json ) && ! empty( $json['error'] ) ? $json['error'] : 'HTTP ' . $code;
			return array( 'ok' => false, 'error' => $msg );
		}

		// API returns download URLs in results — fetch first output (stub-friendly).
		if ( empty( $json['results'][0]['outputs'][0]['url'] ) ) {
			return array( 'ok' => false, 'error' => 'API response missing output URL' );
		}

		$download = wp_remote_get( $json['results'][0]['outputs'][0]['url'], array( 'timeout' => 60 ) );
		if ( is_wp_error( $download ) ) {
			return array( 'ok' => false, 'error' => $download->get_error_message() );
		}

		$out_path = OptiByte_Config::output_dir() . wp_unique_filename(
			OptiByte_Config::output_dir(),
			basename( $source_path, '.' . pathinfo( $source_path, PATHINFO_EXTENSION ) ) . '-ai.' . $formats[0]
		);
		$bytes = wp_remote_retrieve_body( $download );
		if ( ! $bytes ) {
			return array( 'ok' => false, 'error' => 'Empty download from API' );
		}
		file_put_contents( $out_path, $bytes );

		return array( 'ok' => true, 'path' => $out_path );
	}

	/**
	 * Minimal multipart builder for wp_remote_post.
	 *
	 * @param array $body files, formats, sizes, quality, style
	 */
	private static function build_multipart( array $body, $boundary ) {
		$eol  = "\r\n";
		$data = '';

		foreach ( array( 'formats', 'sizes', 'quality', 'style' ) as $key ) {
			if ( ! isset( $body[ $key ] ) ) {
				continue;
			}
			$data .= '--' . $boundary . $eol;
			$data .= 'Content-Disposition: form-data; name="' . $key . '"' . $eol . $eol;
			$data .= $body[ $key ] . $eol;
		}

		foreach ( $body['files'] as $file ) {
			$data .= '--' . $boundary . $eol;
			$data .= 'Content-Disposition: form-data; name="files"; filename="' . $file['name'] . '"' . $eol;
			$data .= 'Content-Type: ' . $file['type'] . $eol . $eol;
			$data .= $file['bits'] . $eol;
		}

		$data .= '--' . $boundary . '--' . $eol;
		return $data;
	}
}
