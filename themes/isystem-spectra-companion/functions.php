<?php
/**
 * iSystem Companion for Spectra — child theme functions.
 *
 * Parent: Spectra One (`spectra-one`)
 *
 * @package iSystem_Spectra_Companion
 * @version 1.1.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Enqueue parent + child stylesheets.
 */
function isystem_enqueue_styles() {
	wp_enqueue_style(
		'parent-style',
		get_template_directory_uri() . '/style.css',
		array(),
		wp_get_theme( get_template() )->get( 'Version' )
	);

	wp_enqueue_style(
		'child-style',
		get_stylesheet_directory_uri() . '/style.css',
		array( 'parent-style' ),
		wp_get_theme()->get( 'Version' )
	);
}
add_action( 'wp_enqueue_scripts', 'isystem_enqueue_styles' );

/**
 * Sidebar fallback block pattern HTML.
 *
 * @return string
 */
function isystem_get_sidebar_fallback_pattern_content() {
	ob_start();
	?>
	<!-- wp:group {"style":{"color":{"background":"#f1f1f1"},"spacing":{"padding":{"top":"1em","right":"1em","bottom":"1em","left":"1em"}}}} -->
	<div class="wp-block-group has-background" style="background-color:#f1f1f1;padding-top:1em;padding-right:1em;padding-bottom:1em;padding-left:1em">
		<!-- wp:paragraph -->
		<p><?php echo esc_html__( 'This is a fallback sidebar block from the iSystem Companion theme.', 'isystem-spectra-companion' ); ?></p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->
	<?php
	return ob_get_clean();
}

/**
 * Register block patterns + category.
 */
function isystem_register_block_patterns() {
	register_block_pattern_category(
		'isystem-patterns',
		array(
			'label' => __( 'iSystem Patterns', 'isystem-spectra-companion' ),
		)
	);

	register_block_pattern(
		'isystem/sidebar-fallback',
		array(
			'title'       => __( 'Sidebar Fallback', 'isystem-spectra-companion' ),
			'description' => __( 'Simple sidebar placeholder for layouts that need a fallback region.', 'isystem-spectra-companion' ),
			'content'     => isystem_get_sidebar_fallback_pattern_content(),
			'categories'  => array( 'isystem-patterns' ),
		)
	);
}
add_action( 'init', 'isystem_register_block_patterns' );

/**
 * Dequeue conflicting admin scripts on post edit screens.
 */
function isystem_dequeue_conflicting_scripts() {
	if ( ! function_exists( 'get_current_screen' ) ) {
		return;
	}
	$screen = get_current_screen();
	if ( ! $screen || 'post' !== $screen->base ) {
		return;
	}

	// WPForms education script can throw on some editor bootstraps.
	wp_dequeue_script( 'wpforms-education-edit-post' );

	// Jetpack connection i18n without bootstrapped state (optional Jetpack install).
	wp_dequeue_script( 'jetpack-connection-i18n' );
}
add_action( 'admin_enqueue_scripts', 'isystem_dequeue_conflicting_scripts', 99 );
