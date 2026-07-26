<?php
/**
 * Plugin Name: FB Member Portal
 * Plugin URI:  https://example.com
 * Description: Fully custom front-end user environment — custom registration, login, roles (Free/Premium), and a member dashboard (Profile, Orders/Payments, Content). No wp-admin exposure for members.
 * Version:     1.6.4
 * Author:      Faham Baloch
 * Author URI:  https://example.com
 * Text Domain: fbmp
 */

/*
 * Code by Faham Baloch
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access.
}

define( 'FBMP_VERSION', '1.6.4' );
define( 'FBMP_PATH', plugin_dir_path( __FILE__ ) );
define( 'FBMP_URL', plugin_dir_url( __FILE__ ) );

require_once FBMP_PATH . 'includes/class-fbmp-roles.php';
require_once FBMP_PATH . 'includes/class-fbmp-db.php';
require_once FBMP_PATH . 'includes/class-fbmp-shortcodes.php';
require_once FBMP_PATH . 'includes/class-fbmp-ajax.php';
require_once FBMP_PATH . 'includes/class-fbmp-admin.php';
require_once FBMP_PATH . 'includes/class-fbmp-referral.php';
require_once FBMP_PATH . 'includes/class-fbmp-stripe.php';
require_once FBMP_PATH . 'includes/class-fbmp-access.php';
require_once FBMP_PATH . 'includes/class-fbmp-presets.php';

/**
 * Activation: create roles, create DB tables, create default pages.
 */
function fbmp_activate() {
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
		update_option( 'fbmp_welcome_email_body', "Hi {name},\n\nYour account has been created successfully.\n\nUsername: {username}\nLogin here: {login_url}\n\nThanks,\n{site_name}" );
	}
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'fbmp_activate' );

/**
 * Deactivation: keep data, just flush rewrite rules.
 */
function fbmp_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'fbmp_deactivate' );

/**
 * Boot everything.
 */
function fbmp_init() {
	FBMP_Roles::init();
	FBMP_Shortcodes::init();
	FBMP_Ajax::init();
	FBMP_Referral::init();
	FBMP_Stripe::init();
	FBMP_Access::init();
	FBMP_Presets::init();
	if ( is_admin() ) {
		FBMP_Admin::init();
	}
}
add_action( 'plugins_loaded', 'fbmp_init' );

/**
 * Enqueue Tailwind (CDN) + our own assets — on pages using our shortcodes,
 * OR on every page if a site-wide Navbar/Footer preset is active (since
 * those render on every page via wp_body_open/wp_footer).
 */
function fbmp_enqueue_assets() {
	global $post;

	$sitewide_active = get_option( 'fbmp_sitewide_navbar', '' ) || get_option( 'fbmp_sitewide_footer', '' );
	$force_everywhere = '1' === get_option( 'fbmp_force_global_css', '0' );

	$has_shortcode = false;
	if ( is_a( $post, 'WP_Post' ) ) {
		$has_shortcode = has_shortcode( $post->post_content, 'fbmp_login' )
			|| has_shortcode( $post->post_content, 'fbmp_register' )
			|| has_shortcode( $post->post_content, 'fbmp_dashboard' )
			|| has_shortcode( $post->post_content, 'fbmp_preset' );

		// Elementor renders from its own _elementor_data meta, bypassing
		// post_content — so also check there for our shortcode names.
		if ( ! $has_shortcode ) {
			$elementor_data = get_post_meta( $post->ID, '_elementor_data', true );
			if ( $elementor_data && (
				false !== strpos( $elementor_data, 'fbmp_login' )
				|| false !== strpos( $elementor_data, 'fbmp_register' )
				|| false !== strpos( $elementor_data, 'fbmp_dashboard' )
				|| false !== strpos( $elementor_data, 'fbmp_preset' )
			) ) {
				$has_shortcode = true;
			}
		}
	}

	if ( ! $has_shortcode && ! $sitewide_active && ! $force_everywhere ) {
		return;
	}

	// Tailwind Play CDN — fine for most sites. For production at scale, swap
	// this for a compiled Tailwind build (see readme.txt).
	wp_enqueue_script( 'fbmp-tailwind-cdn', 'https://cdn.tailwindcss.com', array(), null, false );

	if ( ! $has_shortcode ) {
		return; // sitewide nav/footer only needs Tailwind, not the dashboard/login JS
	}

	wp_enqueue_script( 'fbmp-scripts', FBMP_URL . 'assets/js/fbmp-scripts.js', array( 'jquery' ), FBMP_VERSION, true );
	wp_localize_script(
		'fbmp-scripts',
		'FBMP',
		array(
			'ajax_url'    => admin_url( 'admin-ajax.php' ),
			'nonce'       => wp_create_nonce( 'fbmp_nonce' ),
			'dashboard_url' => FBMP_Roles::get_page_url( 'fbmp_dashboard_page_id' ),
			'login_url'     => FBMP_Roles::get_page_url( 'fbmp_login_page_id' ),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'fbmp_enqueue_assets' );
