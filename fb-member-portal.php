<?php
/**
 * Plugin Name: FB Member Portal
 * Plugin URI:  https://github.com/faham112/fb-member-portal
 * Description: Fully custom front-end user environment — custom registration, login, roles (Free/Premium), and a member dashboard (Profile, Orders/Payments, Content). No wp-admin exposure for members.
 * Version:     1.6.4
 * Author:      Faham Baloch
 * Author URI:  https://github.com/faham112
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

/* ------------------------------------------------------------------
 * Core classes
 * ----------------------------------------------------------------*/
require_once FBMP_PATH . 'includes/class-fbmp-activator.php';
require_once FBMP_PATH . 'includes/class-fbmp-roles.php';
require_once FBMP_PATH . 'includes/class-fbmp-db.php';
require_once FBMP_PATH . 'includes/class-fbmp-ajax.php';
require_once FBMP_PATH . 'includes/class-fbmp-access.php';
require_once FBMP_PATH . 'includes/class-fbmp-referral.php';
require_once FBMP_PATH . 'includes/class-fbmp-stripe.php';

/* ------------------------------------------------------------------
 * Shortcodes (login / register / dashboard)
 * ----------------------------------------------------------------*/
require_once FBMP_PATH . 'includes/shortcodes/class-fbmp-shortcodes.php';

/* ------------------------------------------------------------------
 * Design presets
 * ----------------------------------------------------------------*/
require_once FBMP_PATH . 'includes/class-fbmp-presets.php';

/* ------------------------------------------------------------------
 * Admin (only loaded in wp-admin)
 * ----------------------------------------------------------------*/
require_once FBMP_PATH . 'includes/class-fbmp-admin.php';

/**
 * Activation / deactivation hooks.
 */
register_activation_hook( __FILE__, array( 'FBMP_Activator', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'FBMP_Activator', 'deactivate' ) );

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

	$sitewide_active  = get_option( 'fbmp_sitewide_navbar', '' ) || get_option( 'fbmp_sitewide_footer', '' );
	$force_everywhere = '1' === get_option( 'fbmp_force_global_css', '0' );

	$has_shortcode = false;
	if ( is_a( $post, 'WP_Post' ) ) {
		$has_shortcode = has_shortcode( $post->post_content, 'fbmp_login' )
			|| has_shortcode( $post->post_content, 'fbmp_register' )
			|| has_shortcode( $post->post_content, 'fbmp_dashboard' )
			|| has_shortcode( $post->post_content, 'fbmp_preset' );

		// Elementor renders from its own _elementor_data meta.
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

	wp_enqueue_script( 'fbmp-tailwind-cdn', 'https://cdn.tailwindcss.com', array(), null, false );

	if ( ! $has_shortcode ) {
		return; // sitewide nav/footer only needs Tailwind
	}

	wp_enqueue_script( 'fbmp-scripts', FBMP_URL . 'assets/js/fbmp-scripts.js', array( 'jquery' ), FBMP_VERSION, true );
	wp_localize_script(
		'fbmp-scripts',
		'FBMP',
		array(
			'ajax_url'      => admin_url( 'admin-ajax.php' ),
			'nonce'         => wp_create_nonce( 'fbmp_nonce' ),
			'dashboard_url' => FBMP_Roles::get_page_url( 'fbmp_dashboard_page_id' ),
			'login_url'     => FBMP_Roles::get_page_url( 'fbmp_login_page_id' ),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'fbmp_enqueue_assets' );
