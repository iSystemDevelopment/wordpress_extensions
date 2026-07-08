<?php
/**
 * Plugin Name:       OptiByte WP
 * Plugin URI:        https://optibyte.isystem.app/
 * Description:       WordPress media optimizer — WebP/AVIF via Imagik (ImageMagick), queue + WP-Cron, optional AI styles via iSystem API. Replaces the archived legacy cron v4 bundle (legacy/v4-archive/).
 * Version:           5.0.1
 * Requires at least: 6.0
 * Requires PHP:      8.0
 * Author:            iSystem Development
 * Author URI:        https://isystem.app/
 * License:           MIT
 * License URI:       https://opensource.org/licenses/MIT
 * Text Domain:       optibyte-wp
 *
 * Copyright (c) 2026 iSystem Development / DIODAC ELECTRONICS
 */

defined( 'ABSPATH' ) || exit;

define( 'OPTIBYTE_WP_VERSION', '5.0.1' );
define( 'OPTIBYTE_WP_FILE', __FILE__ );
define( 'OPTIBYTE_WP_DIR', plugin_dir_path( __FILE__ ) );
define( 'OPTIBYTE_WP_URL', plugin_dir_url( __FILE__ ) );

require_once OPTIBYTE_WP_DIR . 'includes/class-optibyte-config.php';
require_once OPTIBYTE_WP_DIR . 'includes/class-optibyte-queue.php';
require_once OPTIBYTE_WP_DIR . 'includes/class-optibyte-log.php';
require_once OPTIBYTE_WP_DIR . 'includes/class-optibyte-ui-helper.php';
require_once OPTIBYTE_WP_DIR . 'includes/class-optibyte-imagik.php';
require_once OPTIBYTE_WP_DIR . 'includes/class-optibyte-ai-client.php';
require_once OPTIBYTE_WP_DIR . 'includes/class-optibyte-scanner.php';
require_once OPTIBYTE_WP_DIR . 'includes/class-optibyte-optimizer.php';
require_once OPTIBYTE_WP_DIR . 'includes/class-optibyte-admin.php';

register_activation_hook( __FILE__, array( 'OptiByte_Admin', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'OptiByte_Admin', 'deactivate' ) );

add_action( 'plugins_loaded', array( 'OptiByte_Admin', 'init' ) );
add_action( 'optibyte_wp_process_queue', array( 'OptiByte_Optimizer', 'process_queue' ) );
