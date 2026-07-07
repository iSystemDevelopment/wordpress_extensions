<?php
/**
 * OptiByte Imagik — ImageMagick / Imagick engine for local encode + preset filters.
 *
 * "Imagik" is the OptiByte name for the server-side image pipeline (PHP Imagick
 * preferred; CLI `magick` fallback). AI-heavy styles delegate to OptiByte_Ai_Client.
 */

defined( 'ABSPATH' ) || exit;

class OptiByte_Imagik {

	/** @return bool */
	public static function imagick_available() {
		return extension_loaded( 'imagick' ) && class_exists( 'Imagick' );
	}

	/** @return bool */
	public static function cli_available() {
		$bin = self::magick_binary();
		return $bin !== '';
	}

	public static function magick_binary() {
		if ( function_exists( 'exec' ) ) {
			$out = array();
			@exec( 'magick -version 2>&1', $out, $code );
			if ( 0 === $code ) {
				return 'magick';
			}
			@exec( 'convert -version 2>&1', $out, $code );
			if ( 0 === $code ) {
				return 'convert';
			}
		}
		return '';
	}

	/**
	 * Local preset filters (non-AI). Matches optibyte.isystem.app style ids.
	 *
	 * @param string $style none|enhance|cartoon|artistic|vintage|abstract
	 */
	public static function apply_local_style( $source_path, $dest_path, $style ) {
		$style = strtolower( (string) $style );
		if ( 'none' === $style || '' === $style ) {
			return copy( $source_path, $dest_path );
		}

		if ( self::imagick_available() ) {
			try {
				$img = new Imagick( $source_path );
				switch ( $style ) {
					case 'enhance':
						$img->normalizeImage();
						$img->modulateImage( 100, 115, 100 );
						$img->sharpenImage( 1, 0.8 );
						break;
					case 'cartoon':
						$img->quantizeImage( 12, Imagick::COLORSPACE_RGB, 0, false, false );
						$img->edgeImage( 1 );
						break;
					case 'artistic':
						$img->oilPaintImage( 2 );
						break;
					case 'vintage':
						$img->sepiaToneImage( 0.75 * $img->getQuantumRange()['quantumRangeLong'] );
						$img->modulateImage( 100, 90, 100 );
						break;
					case 'abstract':
						$img->swirlImage( 12 );
						$img->modulateImage( 110, 120, 105 );
						break;
				}
				$img->writeImage( $dest_path );
				$img->clear();
				$img->destroy();
				return file_exists( $dest_path );
			} catch ( Exception $e ) {
				return false;
			}
		}

		return copy( $source_path, $dest_path );
	}

	/**
	 * Encode image to WebP or AVIF.
	 *
	 * @param string $format webp|avif
	 */
	public static function encode( $source_path, $dest_path, $format, $quality ) {
		$format = strtolower( $format );
		if ( ! in_array( $format, array( 'webp', 'avif' ), true ) ) {
			return false;
		}

		if ( self::imagick_available() ) {
			try {
				$img = new Imagick( $source_path );
				$img->setImageFormat( $format );
				$img->setImageCompressionQuality( (int) $quality );
				$img->writeImage( $dest_path );
				$img->clear();
				$img->destroy();
				return file_exists( $dest_path );
			} catch ( Exception $e ) {
				// fall through to CLI
			}
		}

		$bin = self::magick_binary();
		if ( $bin && function_exists( 'exec' ) ) {
			$src = escapeshellarg( $source_path );
			$dst = escapeshellarg( $dest_path );
			$q   = (int) $quality;
			if ( 'convert' === $bin && 'avif' === $format ) {
				$bin = 'magick';
			}
			$cmd = sprintf( '%s %s -quality %d %s 2>&1', $bin, $src, $q, $dst );
			@exec( $cmd, $out, $code );
			return 0 === $code && file_exists( $dest_path );
		}

		return false;
	}

	public static function engine_label() {
		if ( self::imagick_available() ) {
			return 'PHP Imagick';
		}
		if ( self::cli_available() ) {
			return 'ImageMagick CLI (' . self::magick_binary() . ')';
		}
		return 'unavailable — install php-imagick or ImageMagick';
	}
}
