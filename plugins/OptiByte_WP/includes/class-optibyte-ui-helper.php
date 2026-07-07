<?php
/**
 * OptiByte WP — admin table helpers.
 */

defined( 'ABSPATH' ) || exit;

class OptiByte_UI_Helper {

	public static function status_badge( $status ) {
		$colors = array(
			'pending'    => '#d97706',
			'processing' => '#2563eb',
			'done'       => '#16a34a',
			'error'      => '#dc2626',
		);
		$color  = $colors[ $status ] ?? '#64748b';
		$label  = strtoupper( (string) $status );
		return '<span class="optibyte-status" style="color:' . esc_attr( $color ) . ';font-weight:600;">' . esc_html( $label ) . '</span>';
	}

	public static function format_size( $bytes ) {
		$bytes = (int) $bytes;
		if ( $bytes < 1024 ) {
			return $bytes . ' B';
		}
		return round( $bytes / 1024, 1 ) . ' KB';
	}

	public static function format_time( $iso ) {
		if ( ! $iso ) {
			return '—';
		}
		$ts = strtotime( (string) $iso );
		return $ts ? gmdate( 'Y-m-d H:i', $ts ) . ' UTC' : '—';
	}
}
