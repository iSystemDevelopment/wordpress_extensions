<?php
/**
 * Plugin Name:       DB Cleaner Pro
 * Plugin URI:        https://isystem.app/
 * Description:       Safe database maintenance: expired transients, auto-drafts, spam/trash comments, and orphaned meta. Does not alter SEO URLs or taxonomy.
 * Version:           2.2.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            iSystem Development
 * Author URI:        https://isystem.app/
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       db-cleaner-pro
 *
 * Copyright (c) 2026 iSystem Development / DIODAC ELECTRONICS
 * Also available under MIT for non-.org distributions.
 */

defined( 'ABSPATH' ) || exit;

define( 'DB_CLEANER_PRO_VERSION', '2.2.0' );
define( 'DB_CLEANER_PRO_CRON', 'db_cleaner_pro_weekly' );

register_activation_hook( __FILE__, 'db_cleaner_pro_activate' );
register_deactivation_hook( __FILE__, 'db_cleaner_pro_deactivate' );

/**
 * Schedule weekly cleanup; migrate legacy cron hook name if present.
 */
function db_cleaner_pro_activate() {
	// Drop older hook name from earlier releases.
	wp_clear_scheduled_hook( 'weekly_database_cleanup' );

	if ( ! wp_next_scheduled( DB_CLEANER_PRO_CRON ) ) {
		wp_schedule_event( time() + HOUR_IN_SECONDS, 'weekly', DB_CLEANER_PRO_CRON );
	}
}

/**
 * Clear cron on deactivate.
 */
function db_cleaner_pro_deactivate() {
	wp_clear_scheduled_hook( DB_CLEANER_PRO_CRON );
	wp_clear_scheduled_hook( 'weekly_database_cleanup' );
}

add_action( DB_CLEANER_PRO_CRON, 'db_cleaner_pro_run_cleanup' );

/**
 * @return string Log path under wp-content.
 */
function db_cleaner_pro_log_path() {
	return WP_CONTENT_DIR . '/db-cleaner-pro.log';
}

/**
 * Append one line to the cleanup log (best-effort).
 *
 * @param string $message Log line.
 */
function db_cleaner_pro_log( $message ) {
	$path = db_cleaner_pro_log_path();
	$entry = '[' . current_time( 'mysql' ) . '] ' . $message . PHP_EOL;

	// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents -- admin maintenance log.
	@file_put_contents( $path, $entry, FILE_APPEND | LOCK_EX );
}

/**
 * Run safe cleanup jobs. Intended for cron + Tools admin.
 *
 * @return array{ok:bool,summary:string} Result summary for UI/log.
 */
function db_cleaner_pro_run_cleanup() {
	global $wpdb;

	$counts = array(
		'transients'     => 0,
		'auto_drafts'    => 0,
		'comments'       => 0,
		'commentmeta'    => 0,
		'postmeta'       => 0,
		'tables'         => 0,
	);

	// Core helper removes expired transients without wiping warm, still-valid cache.
	if ( function_exists( 'delete_expired_transients' ) ) {
		delete_expired_transients( true );
		$counts['transients'] = 1; // API does not return a count.
	} else {
		// Fallback for very old WP: expire timeout rows, then orphaned value rows.
		$counts['transients'] += (int) $wpdb->query(
			"DELETE FROM {$wpdb->options}
			 WHERE option_name LIKE '\\_transient\\_timeout\\_%'
			   AND option_value < UNIX_TIMESTAMP()"
		);
		$counts['transients'] += (int) $wpdb->query(
			"DELETE FROM {$wpdb->options}
			 WHERE option_name LIKE '\\_site\\_transient\\_timeout\\_%'
			   AND option_value < UNIX_TIMESTAMP()"
		);
		$counts['transients'] += (int) $wpdb->query(
			"DELETE o FROM {$wpdb->options} o
			 LEFT JOIN {$wpdb->options} t
			   ON t.option_name = CONCAT('_transient_timeout_', SUBSTRING(o.option_name, 12))
			 WHERE o.option_name LIKE '\\_transient\\_%'
			   AND o.option_name NOT LIKE '\\_transient\\_timeout\\_%'
			   AND t.option_id IS NULL"
		);
		$counts['transients'] += (int) $wpdb->query(
			"DELETE o FROM {$wpdb->options} o
			 LEFT JOIN {$wpdb->options} t
			   ON t.option_name = CONCAT('_site_transient_timeout_', SUBSTRING(o.option_name, 17))
			 WHERE o.option_name LIKE '\\_site\\_transient\\_%'
			   AND o.option_name NOT LIKE '\\_site\\_transient\\_timeout\\_%'
			   AND t.option_id IS NULL"
		);
	}

	$counts['auto_drafts'] = (int) $wpdb->query(
		$wpdb->prepare(
			"DELETE FROM {$wpdb->posts} WHERE post_status = %s",
			'auto-draft'
		)
	);

	$counts['comments'] = (int) $wpdb->query(
		"DELETE FROM {$wpdb->comments} WHERE comment_approved IN ('spam','trash')"
	);

	$counts['commentmeta'] = (int) $wpdb->query(
		"DELETE cm FROM {$wpdb->commentmeta} cm
		 LEFT JOIN {$wpdb->comments} c ON c.comment_ID = cm.comment_id
		 WHERE c.comment_ID IS NULL"
	);

	$counts['postmeta'] = (int) $wpdb->query(
		"DELETE pm FROM {$wpdb->postmeta} pm
		 LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id
		 WHERE p.ID IS NULL"
	);

	// OPTIMIZE can lock large tables — keep to core tables only.
	$tables = array(
		'commentmeta',
		'comments',
		'options',
		'postmeta',
		'posts',
		'termmeta',
		'terms',
		'term_relationships',
		'term_taxonomy',
	);
	foreach ( $tables as $table ) {
		$name = $wpdb->prefix . $table;
		// Table names cannot be prepared placeholders; whitelist only prefix+known slug.
		$wpdb->query( "OPTIMIZE TABLE `{$name}`" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$counts['tables']++;
	}

	$summary = sprintf(
		'Cleanup OK — auto-drafts:%d comments:%d orphan commentmeta:%d orphan postmeta:%d tables optimized:%d (expired transients purged).',
		$counts['auto_drafts'],
		$counts['comments'],
		$counts['commentmeta'],
		$counts['postmeta'],
		$counts['tables']
	);
	db_cleaner_pro_log( $summary );

	return array(
		'ok'      => true,
		'summary' => $summary,
	);
}

add_action(
	'admin_menu',
	static function () {
		add_management_page(
			__( 'DB Cleaner Pro', 'db-cleaner-pro' ),
			__( 'DB Cleaner', 'db-cleaner-pro' ),
			'manage_options',
			'db-cleaner-pro',
			'db_cleaner_pro_admin_page'
		);
	}
);

/**
 * Tools → DB Cleaner admin UI.
 */
function db_cleaner_pro_admin_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Insufficient permissions.', 'db-cleaner-pro' ) );
	}

	$log_file    = db_cleaner_pro_log_path();
	$log_content = file_exists( $log_file ) ? (string) file_get_contents( $log_file ) : __( 'No logs yet.', 'db-cleaner-pro' );
	$notice      = '';

	if ( isset( $_POST['db_cleaner_pro_run'] ) ) {
		check_admin_referer( 'db_cleaner_pro_run' );
		$result      = db_cleaner_pro_run_cleanup();
		$log_content = file_exists( $log_file ) ? (string) file_get_contents( $log_file ) : '';
		$notice      = $result['summary'];
	}

	$next = wp_next_scheduled( DB_CLEANER_PRO_CRON );
	?>
	<div class="wrap">
		<h1><?php echo esc_html( sprintf( 'DB Cleaner Pro %s', DB_CLEANER_PRO_VERSION ) ); ?></h1>
		<p class="description">
			<?php esc_html_e( 'Removes expired transients, auto-drafts, spam/trash comments, and orphaned meta. Taxonomy and SEO URL data are left alone.', 'db-cleaner-pro' ); ?>
		</p>
		<?php if ( $notice ) : ?>
			<div class="notice notice-success is-dismissible"><p><strong><?php echo esc_html( $notice ); ?></strong></p></div>
		<?php endif; ?>
		<p>
			<?php
			if ( $next ) {
				printf(
					/* translators: %s: local datetime */
					esc_html__( 'Next scheduled run: %s', 'db-cleaner-pro' ),
					esc_html( wp_date( 'Y-m-d H:i:s', $next ) )
				);
			} else {
				esc_html_e( 'Weekly cron is not scheduled. Deactivate/reactivate the plugin or run cleanup now.', 'db-cleaner-pro' );
			}
			?>
		</p>
		<form method="post">
			<?php wp_nonce_field( 'db_cleaner_pro_run' ); ?>
			<p>
				<button type="submit" name="db_cleaner_pro_run" value="1" class="button button-primary"
					onclick="return confirm('<?php echo esc_js( __( 'Run database cleanup now?', 'db-cleaner-pro' ) ); ?>');">
					<?php esc_html_e( 'Run cleanup now', 'db-cleaner-pro' ); ?>
				</button>
			</p>
		</form>
		<h2 style="margin-top:2rem;"><?php esc_html_e( 'Cleanup log', 'db-cleaner-pro' ); ?></h2>
		<textarea readonly rows="14" class="large-text code" style="font-family:monospace;"><?php echo esc_textarea( $log_content ); ?></textarea>
		<p class="description"><code><?php echo esc_html( $log_file ); ?></code></p>
	</div>
	<?php
}
