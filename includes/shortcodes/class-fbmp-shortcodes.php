<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Shortcode loader — registers all front-end shortcodes.
 */
class FBMP_Shortcodes {

	public static function init() {
		add_shortcode( 'fbmp_login', array( __CLASS__, 'render_login' ) );
		add_shortcode( 'fbmp_register', array( __CLASS__, 'render_register' ) );
		add_shortcode( 'fbmp_dashboard', array( __CLASS__, 'render_dashboard' ) );
	}

	public static function render_login() {
		require_once FBMP_PATH . 'includes/shortcodes/login.php';
		return fbmp_render_login_form();
	}

	public static function render_register() {
		require_once FBMP_PATH . 'includes/shortcodes/register.php';
		return fbmp_render_register_form();
	}

	public static function render_dashboard() {
		require_once FBMP_PATH . 'includes/shortcodes/dashboard.php';
		return fbmp_render_dashboard();
	}
}
