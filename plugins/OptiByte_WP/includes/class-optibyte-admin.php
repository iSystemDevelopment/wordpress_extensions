<?php
/**
 * OptiByte WP — admin UI, settings, WP-Cron.
 */

defined( 'ABSPATH' ) || exit;

class OptiByte_Admin {

	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'register_menu' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
		add_action( 'add_attachment', array( __CLASS__, 'maybe_queue_attachment' ) );
	}

	public static function activate() {
		OptiByte_Config::get();
		OptiByte_Config::staging_dir();
		OptiByte_Config::output_dir();
		if ( ! wp_next_scheduled( 'optibyte_wp_process_queue' ) ) {
			wp_schedule_event( time() + 60, 'hourly', 'optibyte_wp_process_queue' );
		}
	}

	public static function deactivate() {
		wp_clear_scheduled_hook( 'optibyte_wp_process_queue' );
	}

	public static function register_menu() {
		// Settings + API token: require manage_options, not just upload_files.
		add_media_page(
			__( 'OptiByte', 'optibyte-wp' ),
			__( 'OptiByte', 'optibyte-wp' ),
			'manage_options',
			'optibyte-wp',
			array( __CLASS__, 'render_page' )
		);
	}

	public static function enqueue_assets( $hook ) {
		if ( 'media_page_optibyte-wp' !== $hook ) {
			return;
		}
		wp_enqueue_style(
			'optibyte-wp-admin',
			OPTIBYTE_WP_URL . 'assets/admin.css',
			array(),
			OPTIBYTE_WP_VERSION
		);
	}

	public static function maybe_queue_attachment( $attachment_id ) {
		$cfg = OptiByte_Config::get();
		if ( empty( $cfg['auto_scan_uploads'] ) ) {
			return;
		}
		OptiByte_Scanner::queue_attachment( (int) $attachment_id );
	}

	public static function render_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Insufficient permissions.', 'optibyte-wp' ) );
		}

		self::handle_post();

		$cfg   = OptiByte_Config::get();
		$queue = OptiByte_Queue::load();
		$logs  = OptiByte_Log::load();

		include OPTIBYTE_WP_DIR . 'admin/views/dashboard.php';
	}

	private static function handle_post() {
		if ( ! isset( $_POST['optibyte_wp_action'] ) ) {
			return;
		}
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		check_admin_referer( 'optibyte_wp_dashboard' );

		$action = sanitize_key( wp_unslash( $_POST['optibyte_wp_action'] ) );

		if ( 'save_settings' === $action ) {
			$formats = isset( $_POST['formats'] ) && is_array( $_POST['formats'] )
				? array_map( 'sanitize_key', wp_unslash( $_POST['formats'] ) )
				: array( 'webp' );
			$formats = array_values( array_intersect( $formats, array( 'webp', 'avif' ) ) );
			if ( ! $formats ) {
				$formats = array( 'webp' );
			}

			OptiByte_Config::update(
				array(
					'webp_quality'      => min( 100, max( 50, (int) $_POST['webp_quality'] ) ),
					'avif_quality'      => min( 100, max( 40, (int) $_POST['avif_quality'] ) ),
					'formats'           => $formats,
					'default_style'     => sanitize_key( wp_unslash( $_POST['default_style'] ?? 'none' ) ),
					'api_base'          => esc_url_raw( wp_unslash( $_POST['api_base'] ?? '' ) ),
					'api_service_token' => sanitize_text_field( wp_unslash( $_POST['api_service_token'] ?? '' ) ),
					'auto_scan_uploads' => ! empty( $_POST['auto_scan_uploads'] ),
				)
			);
			add_settings_error( 'optibyte_wp', 'saved', __( 'Settings saved.', 'optibyte-wp' ), 'success' );
			return;
		}

		if ( 'scan' === $action ) {
			$added = OptiByte_Scanner::scan();
			add_settings_error( 'optibyte_wp', 'scan', sprintf( __( '%d job(s) queued.', 'optibyte-wp' ), $added ), 'info' );
		} elseif ( 'optimize' === $action ) {
			$done = OptiByte_Optimizer::process_queue();
			add_settings_error( 'optibyte_wp', 'optimize', sprintf( __( '%d image(s) optimized.', 'optibyte-wp' ), $done ), 'success' );
		} elseif ( 'clear_queue' === $action ) {
			OptiByte_Queue::clear();
			add_settings_error( 'optibyte_wp', 'clear_q', __( 'Queue cleared.', 'optibyte-wp' ), 'info' );
		} elseif ( 'clear_log' === $action ) {
			OptiByte_Log::clear();
			add_settings_error( 'optibyte_wp', 'clear_l', __( 'Log cleared.', 'optibyte-wp' ), 'info' );
		}
	}
}
