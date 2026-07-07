<?php
/**
 * OptiByte WP admin dashboard.
 *
 * @var array $cfg
 * @var array $queue
 * @var array $logs
 */

defined( 'ABSPATH' ) || exit;

settings_errors( 'optibyte_wp' );
?>
<div class="wrap optibyte-wrap">
	<h1><?php esc_html_e( 'OptiByte WP', 'optibyte-wp' ); ?> <span class="optibyte-version">v<?php echo esc_html( OPTIBYTE_WP_VERSION ); ?></span></h1>
	<p class="description">
		<?php esc_html_e( 'Media optimizer for WordPress — Imagik (ImageMagick) local encode + optional AI styles via iSystem API.', 'optibyte-wp' ); ?>
		<a href="https://optibyte.isystem.app/" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'OptiByte Studio', 'optibyte-wp' ); ?></a>
	</p>

	<div class="optibyte-engine">
		<strong><?php esc_html_e( 'Imagik engine:', 'optibyte-wp' ); ?></strong>
		<?php echo esc_html( OptiByte_Imagik::engine_label() ); ?>
		<?php if ( OptiByte_Ai_Client::configured() ) : ?>
			· <span class="optibyte-ok"><?php esc_html_e( 'AI API token set', 'optibyte-wp' ); ?></span>
		<?php else : ?>
			· <span class="optibyte-warn"><?php esc_html_e( 'AI styles use local Imagik presets until API token is set', 'optibyte-wp' ); ?></span>
		<?php endif; ?>
	</div>

	<form method="post" class="optibyte-actions">
		<?php wp_nonce_field( 'optibyte_wp_dashboard' ); ?>
		<input type="hidden" name="optibyte_wp_action" value="scan" />
		<button type="submit" class="button button-primary"><?php esc_html_e( 'Scan staging folder', 'optibyte-wp' ); ?></button>
	</form>
	<form method="post" class="optibyte-actions">
		<?php wp_nonce_field( 'optibyte_wp_dashboard' ); ?>
		<input type="hidden" name="optibyte_wp_action" value="optimize" />
		<button type="submit" class="button"><?php esc_html_e( 'Run optimizer now', 'optibyte-wp' ); ?></button>
	</form>
	<form method="post" class="optibyte-actions" onsubmit="return confirm('<?php echo esc_js( __( 'Clear queue?', 'optibyte-wp' ) ); ?>');">
		<?php wp_nonce_field( 'optibyte_wp_dashboard' ); ?>
		<input type="hidden" name="optibyte_wp_action" value="clear_queue" />
		<button type="submit" class="button"><?php esc_html_e( 'Clear queue', 'optibyte-wp' ); ?></button>
	</form>
	<form method="post" class="optibyte-actions" onsubmit="return confirm('<?php echo esc_js( __( 'Clear log?', 'optibyte-wp' ) ); ?>');">
		<?php wp_nonce_field( 'optibyte_wp_dashboard' ); ?>
		<input type="hidden" name="optibyte_wp_action" value="clear_log" />
		<button type="submit" class="button"><?php esc_html_e( 'Clear log', 'optibyte-wp' ); ?></button>
	</form>

	<h2><?php esc_html_e( 'Settings', 'optibyte-wp' ); ?></h2>
	<form method="post" class="optibyte-settings">
		<?php wp_nonce_field( 'optibyte_wp_dashboard' ); ?>
		<input type="hidden" name="optibyte_wp_action" value="save_settings" />
		<table class="form-table" role="presentation">
			<tr>
				<th scope="row"><?php esc_html_e( 'Output formats', 'optibyte-wp' ); ?></th>
				<td>
					<label><input type="checkbox" name="formats[]" value="webp" <?php checked( in_array( 'webp', $cfg['formats'], true ) ); ?> /> WebP</label>
					<label style="margin-left:12px;"><input type="checkbox" name="formats[]" value="avif" <?php checked( in_array( 'avif', $cfg['formats'], true ) ); ?> /> AVIF</label>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="webp_quality"><?php esc_html_e( 'WebP quality', 'optibyte-wp' ); ?></label></th>
				<td><input name="webp_quality" id="webp_quality" type="number" min="50" max="100" value="<?php echo esc_attr( $cfg['webp_quality'] ); ?>" /></td>
			</tr>
			<tr>
				<th scope="row"><label for="avif_quality"><?php esc_html_e( 'AVIF quality', 'optibyte-wp' ); ?></label></th>
				<td><input name="avif_quality" id="avif_quality" type="number" min="40" max="100" value="<?php echo esc_attr( $cfg['avif_quality'] ); ?>" /></td>
			</tr>
			<tr>
				<th scope="row"><label for="default_style"><?php esc_html_e( 'Default AI / Imagik style', 'optibyte-wp' ); ?></label></th>
				<td>
					<select name="default_style" id="default_style">
						<?php foreach ( OptiByte_Config::ai_styles() as $style ) : ?>
							<option value="<?php echo esc_attr( $style ); ?>" <?php selected( $cfg['default_style'], $style ); ?>><?php echo esc_html( ucfirst( $style ) ); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Auto-queue uploads', 'optibyte-wp' ); ?></th>
				<td><label><input type="checkbox" name="auto_scan_uploads" value="1" <?php checked( ! empty( $cfg['auto_scan_uploads'] ) ); ?> /> <?php esc_html_e( 'Queue new Media Library images on upload', 'optibyte-wp' ); ?></label></td>
			</tr>
			<tr>
				<th scope="row"><label for="api_base"><?php esc_html_e( 'API base URL', 'optibyte-wp' ); ?></label></th>
				<td><input name="api_base" id="api_base" type="url" class="regular-text" value="<?php echo esc_attr( $cfg['api_base'] ); ?>" /></td>
			</tr>
			<tr>
				<th scope="row"><label for="api_service_token"><?php esc_html_e( 'Service token', 'optibyte-wp' ); ?></label></th>
				<td>
					<input name="api_service_token" id="api_service_token" type="password" class="regular-text" value="<?php echo esc_attr( $cfg['api_service_token'] ); ?>" autocomplete="off" />
					<p class="description"><?php esc_html_e( 'Bearer token for api.isystem.app OptiByte routes (site-to-site). See STUBS.md.', 'optibyte-wp' ); ?></p>
				</td>
			</tr>
		</table>
		<?php submit_button( __( 'Save settings', 'optibyte-wp' ) ); ?>
	</form>

	<h2><?php esc_html_e( 'Job queue', 'optibyte-wp' ); ?> (<?php echo count( $queue ); ?>)</h2>
	<table class="widefat striped">
		<thead><tr><th><?php esc_html_e( 'File', 'optibyte-wp' ); ?></th><th><?php esc_html_e( 'Style', 'optibyte-wp' ); ?></th><th><?php esc_html_e( 'Status', 'optibyte-wp' ); ?></th><th><?php esc_html_e( 'Started', 'optibyte-wp' ); ?></th><th><?php esc_html_e( 'Completed', 'optibyte-wp' ); ?></th></tr></thead>
		<tbody>
		<?php if ( ! $queue ) : ?>
			<tr><td colspan="5"><?php esc_html_e( 'No jobs yet. Drop images in staging or enable auto-queue.', 'optibyte-wp' ); ?></td></tr>
		<?php else : ?>
			<?php foreach ( $queue as $job ) : ?>
			<tr>
				<td><?php echo esc_html( $job['file'] ?? '' ); ?></td>
				<td><?php echo esc_html( $job['style'] ?? 'none' ); ?></td>
				<td><?php echo wp_kses_post( OptiByte_UI_Helper::status_badge( $job['status'] ?? 'pending' ) ); ?></td>
				<td><?php echo esc_html( OptiByte_UI_Helper::format_time( $job['started'] ?? null ) ); ?></td>
				<td><?php echo esc_html( OptiByte_UI_Helper::format_time( $job['completed'] ?? null ) ); ?></td>
			</tr>
			<?php endforeach; ?>
		<?php endif; ?>
		</tbody>
	</table>

	<h2><?php esc_html_e( 'Optimization log', 'optibyte-wp' ); ?> (<?php echo count( $logs ); ?>)</h2>
	<table class="widefat striped">
		<thead><tr><th><?php esc_html_e( 'File', 'optibyte-wp' ); ?></th><th><?php esc_html_e( 'Status', 'optibyte-wp' ); ?></th><th><?php esc_html_e( 'Style', 'optibyte-wp' ); ?></th><th><?php esc_html_e( 'WebP', 'optibyte-wp' ); ?></th><th><?php esc_html_e( 'AVIF', 'optibyte-wp' ); ?></th><th><?php esc_html_e( 'Engine', 'optibyte-wp' ); ?></th><th><?php esc_html_e( 'ms', 'optibyte-wp' ); ?></th></tr></thead>
		<tbody>
		<?php foreach ( array_reverse( $logs ) as $entry ) : ?>
			<tr>
				<td><?php echo esc_html( $entry['file'] ?? '' ); ?></td>
				<td><?php echo wp_kses_post( OptiByte_UI_Helper::status_badge( $entry['status'] ?? '' ) ); ?></td>
				<td><?php echo esc_html( $entry['style'] ?? '—' ); ?></td>
				<td><?php echo esc_html( OptiByte_UI_Helper::format_size( $entry['webp_size'] ?? 0 ) ); ?></td>
				<td><?php echo esc_html( OptiByte_UI_Helper::format_size( $entry['avif_size'] ?? 0 ) ); ?></td>
				<td><?php echo esc_html( $entry['engine'] ?? '—' ); ?></td>
				<td><?php echo esc_html( (string) ( $entry['duration_ms'] ?? 0 ) ); ?></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>

	<p class="description optibyte-paths">
		<?php
		printf(
			/* translators: 1: staging path 2: output path */
			esc_html__( 'Staging: %1$s · Output: %2$s', 'optibyte-wp' ),
			'<code>' . esc_html( OptiByte_Config::staging_dir() ) . '</code>',
			'<code>' . esc_html( OptiByte_Config::output_dir() ) . '</code>'
		);
		?>
	</p>
</div>
