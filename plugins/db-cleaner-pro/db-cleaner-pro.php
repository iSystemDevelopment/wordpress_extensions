<?php
/**
 * Plugin Name: DB Cleaner Pro
 * Description: Safe database-only cleaner: clears transients, orphaned data, and spam/trash. SEO and taxonomy untouched.
 * Version: 2.1.1
 * Author: iSystem Development
 * License: MIT
 * Text Domain: db-cleaner-pro
 */

defined( 'ABSPATH' ) || exit;

register_activation_hook( __FILE__, function () {
	if ( ! wp_next_scheduled( 'weekly_database_cleanup' ) ) {
		wp_schedule_event( time(), 'weekly', 'weekly_database_cleanup' );
	}
} );

register_deactivation_hook( __FILE__, function () {
	wp_clear_scheduled_hook( 'weekly_database_cleanup' );
} );

add_action( 'weekly_database_cleanup', 'db_cleaner_pro_run_cleanup' );

/**
 * @return string Log file path under wp-content.
 */
function db_cleaner_pro_log_path() {
	return WP_CONTENT_DIR . '/db-cleaner-pro.log';
}

/**
 * @param string $message Log line.
 */
function db_cleaner_pro_log( $message ) {
	$entry = '[' . current_time( 'mysql' ) . '] ' . $message . PHP_EOL;
	file_put_contents( db_cleaner_pro_log_path(), $entry, FILE_APPEND | LOCK_EX );
}

function db_cleaner_pro_run_cleanup() {
	global $wpdb;

	$wpdb->query( "DELETE FROM `{$wpdb->options}` WHERE `option_name` LIKE '_transient_%' AND `option_name` NOT LIKE '_transient_timeout_%'" );
	$wpdb->query( "DELETE FROM `{$wpdb->options}` WHERE `option_name` LIKE '_site_transient_%' AND `option_name` NOT LIKE '_site_transient_timeout_%'" );
	$wpdb->query( "DELETE FROM `{$wpdb->options}` WHERE `option_name` LIKE '_transient_timeout_%' AND `option_value` < UNIX_TIMESTAMP()" );
	$wpdb->query( "DELETE FROM `{$wpdb->options}` WHERE `option_name` LIKE '_site_transient_timeout_%' AND `option_value` < UNIX_TIMESTAMP()" );
	$wpdb->query( "DELETE FROM `{$wpdb->options}` WHERE `autoload` = 'off' AND LENGTH(`option_value`) > 50000" );

	$wpdb->query( "DELETE FROM `{$wpdb->posts}` WHERE `post_status` = 'auto-draft'" );
	$wpdb->query( "DELETE FROM `{$wpdb->comments}` WHERE `comment_approved` IN ('spam', 'trash')" );
	$wpdb->query( "DELETE cm FROM `{$wpdb->commentmeta}` cm LEFT JOIN `{$wpdb->comments}` c ON c.comment_ID = cm.comment_id WHERE c.comment_ID IS NULL" );
	$wpdb->query( "DELETE pm FROM `{$wpdb->postmeta}` pm LEFT JOIN `{$wpdb->posts}` p ON p.ID = pm.post_id WHERE p.ID IS NULL" );

	$tables = array( 'commentmeta', 'comments', 'links', 'options', 'postmeta', 'posts', 'termmeta', 'terms', 'term_relationships', 'term_taxonomy', 'usermeta', 'users' );
	foreach ( $tables as $table ) {
		$wpdb->query( "OPTIMIZE TABLE `{$wpdb->prefix}$table`" );
	}

	db_cleaner_pro_log( 'Safe DB cleanup completed.' );
}

add_action( 'admin_menu', function () {
	add_management_page(
		'DB Cleaner',
		'DB Cleaner',
		'manage_options',
		'db-cleaner-pro',
		'db_cleaner_pro_admin_page'
	);
} );

function db_cleaner_pro_admin_page() {
	$log_file = db_cleaner_pro_log_path();
	$log_content = file_exists( $log_file ) ? file_get_contents( $log_file ) : 'No logs yet.';

	if ( isset( $_POST['db_cleaner_pro_run'] ) && check_admin_referer( 'db_cleaner_pro_run' ) ) {
		db_cleaner_pro_run_cleanup();
		$log_content = file_exists( $log_file ) ? file_get_contents( $log_file ) : '';
		echo '<div class="notice notice-success is-dismissible"><p><strong>DB cleanup completed.</strong></p></div>';
	}

	?>
	<div class="wrap">
		<h1>DB Cleaner Pro</h1>
		<form method="post">
			<?php wp_nonce_field( 'db_cleaner_pro_run' ); ?>
			<p><button type="submit" name="db_cleaner_pro_run" value="1" class="button button-primary">Run cleanup now</button></p>
		</form>
		<h2 style="margin-top:2rem;">Cleanup log</h2>
		<textarea readonly rows="12" style="width:100%;font-family:monospace;"><?php echo esc_textarea( $log_content ); ?></textarea>
	</div>
	<?php
}
