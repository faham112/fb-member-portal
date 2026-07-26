<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles plugin activation, deactivation and default options.
 */
class FBMP_Activator {

	/**
	 * Run on plugin activation.
	 */
	public static function activate() {
		FBMP_Roles::register_roles();
		FBMP_DB::create_tables();
		FBMP_Roles::maybe_create_pages();

		if ( false === get_option( 'fbmp_premium_category', false ) ) {
			update_option( 'fbmp_premium_category', 'premium' );
		}
		if ( false === get_option( 'fbmp_after_register_redirect', false ) ) {
			update_option( 'fbmp_after_register_redirect', 'dashboard' );
		}
		if ( false === get_option( 'fbmp_send_welcome_email', false ) ) {
			update_option( 'fbmp_send_welcome_email', '1' );
			update_option( 'fbmp_welcome_email_subject', 'Welcome to {site_name}!' );
			update_option(
				'fbmp_welcome_email_body',
				"Hi {name},\n\nYour account has been created successfully.\n\nUsername: {username}\nLogin here: {login_url}\n\nThanks,\n{site_name}"
			);
		}

		flush_rewrite_rules();
	}

	/**
	 * Run on plugin deactivation.
	 */
	public static function deactivate() {
		flush_rewrite_rules();
	}
}
