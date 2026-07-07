<?php
/**
 * iSystem Companion for Spectra
 *
 * This file merges the original child theme functions with the
 * admin optimizations from the GCC Plus plugin.
 *
 * @package iSystem_Spectra_Companion
 */

// Prevent direct access to the file.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Enqueues the parent and child theme stylesheets.
 */
function isystem_enqueue_styles() {
    // Enqueue the parent theme's stylesheet (Spectra One).
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );

    // Enqueue the child theme's stylesheet, making it dependent on the parent.
    wp_enqueue_style(
        'child-style',
        get_stylesheet_directory_uri() . '/style.css',
        [ 'parent-style' ]
    );
}
add_action( 'wp_enqueue_scripts', 'isystem_enqueue_styles' );

/**
 * Returns the HTML content for the sidebar fallback block pattern.
 *
 * @return string The block pattern HTML content.
 */
function isystem_get_sidebar_fallback_pattern_content() {
    // Use output buffering to capture the HTML.
    ob_start();
    ?>
    <div class="wp-block-group has-background" style="background-color:#f1f1f1;padding-top:1em;padding-right:1em;padding-bottom:1em;padding-left:1em">
        <p>This is a fallback sidebar block from the iSystem Companion theme.</p>
        </div>
    <?php
    // Return the captured HTML.
    return ob_get_clean();
}

/**
 * Registers custom block patterns and a new pattern category for the theme.
 */
function isystem_register_block_patterns() {
    // Register a unique category for iSystem patterns.
    register_block_pattern_category( 'isystem-patterns', [ 'label' => __( 'iSystem Patterns', 'isystem-spectra-companion' ) ] );

    // Register the sidebar fallback pattern.
    register_block_pattern(
        'isystem/sidebar-fallback',
        [
            'title'      => __( 'Sidebar Fallback', 'isystem-spectra-companion' ),
            'content'    => isystem_get_sidebar_fallback_pattern_content(),
            'categories' => [ 'isystem-patterns' ],
        ]
    );
}
add_action( 'init', 'isystem_register_block_patterns' );

/**
 * Dequeues conflicting admin scripts from other plugins.
 * This function resolves known JavaScript errors on post/page edit screens.
 */
function isystem_dequeue_conflicting_scripts() {
    $screen = get_current_screen();

    // Only run on post and page editing screens.
    if ( $screen && 'post' === $screen->base ) {

        /**
         * FIX FOR WPFORMS:
         * Dequeues a script that can cause 'Cannot read properties of undefined' errors.
         */
        wp_dequeue_script( 'wpforms-education-edit-post' );

        /**
         * FIX FOR JETPACK:
         * Dequeues a script that can cause the "Initial state is missing" error.
         */
        wp_dequeue_script( 'jetpack-connection-i18n' );
    }
}
// Hook into 'admin_enqueue_scripts' with high priority (99) to run after most other scripts are enqueued.
add_action( 'admin_enqueue_scripts', 'isystem_dequeue_conflicting_scripts', 99 );