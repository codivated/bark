<?php
/**
 * Options page for Bark.
 *
 * @package bark
 */

function bark_options_page() {
	add_submenu_page( 'edit.php?post_type=cdv8_bark', 'Bark Options', 'Settings', 'edit_posts', basename( __FILE__ ), 'bark_display_options' );
}
add_action( 'admin_menu', 'bark_options_page' );

function bark_admin_init() {
	register_setting( 'bark', 'bark-limit-logs' );
	register_setting( 'bark', 'bark-enable-background-processing' );

	add_settings_section( 'bark-general-settings', '', 'bark_general_settings_display', 'bark' );

	add_settings_field( 'bark-limit-logs', 'Limit Bark Logs', 'bark_limit_logs_field_display', 'bark', 'bark-general-settings' );
	add_settings_field( 'bark-enable-background-processing', 'Enable Background Processing', 'bark_enable_background_processing_field_display', 'bark', 'bark-general-settings' );
}
add_action( 'admin_init', 'bark_admin_init' );

function bark_general_settings_display() {
	// We don't need to display a title for this settings section right now.
}

function bark_limit_logs_field_display() {
	?>
	<input type="text" name="bark-limit-logs" value="<?php echo intval( get_option( 'bark-limit-logs' ) ); ?>" />
	<p class="description"><?php esc_html_e( 'Prevent Bark from entering new logs into the database once there are XX logs already stored.', 'bark' ); ?></p>
	<p class="description"><?php esc_html_e( 'This is to help mitigate against an error heavy site bogging down the database due to logging thousands of errors.', 'bark' ); ?></p>
	<?php
}

function bark_enable_background_processing_field_display() {
	?>
	<input type="hidden" name="bark-enable-background-processing" value="0" />
	<input type="checkbox" name="bark-enable-background-processing" <?php checked( Bark_Queue_Manager::get_instance()->background_processing_enabled(), 1 ) ; ?>" value="1" />
	<p class="description"><?php esc_html_e( 'Barks will be added to a queue and processed in the background instead of as soon as bark is called.', 'bark' ); ?></p>
	<p class="description"><?php esc_html_e( 'This is useful if you are worried about barks impacting down page load time.', 'bark' ); ?></p>
	<?php
}

function bark_display_options() {
	?>
	<div class="wrap">
		<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
		<p><?php echo esc_html__( 'Configure Bark to meet your needs.', 'bark' ); ?></p>

		<form method="post" action="options.php">
			<?php settings_fields( 'bark' ); ?>
			<?php do_settings_sections( 'bark' ); ?>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}
