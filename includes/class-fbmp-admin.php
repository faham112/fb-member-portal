<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin menu, actions, assets and page routing.
 */
class FBMP_Admin {

	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'register_menu' ) );
		add_action( 'admin_init', array( __CLASS__, 'handle_actions' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_admin_assets' ) );
		add_filter( 'admin_footer_text', array( __CLASS__, 'branded_footer_text' ) );
		add_filter( 'update_footer', array( __CLASS__, 'branded_footer_version' ), 11 );
	}

	public static function branded_footer_text( $text ) {
		$screen = get_current_screen();
		if ( $screen && strpos( $screen->id, 'fbmp' ) !== false ) {
			return 'FB Member Portal — Code by <strong>Faham Baloch</strong>';
		}
		return $text;
	}

	public static function branded_footer_version( $text ) {
		$screen = get_current_screen();
		if ( $screen && strpos( $screen->id, 'fbmp' ) !== false ) {
			return 'v' . FBMP_VERSION;
		}
		return $text;
	}

	public static function enqueue_admin_assets( $hook ) {
		if ( strpos( $hook, 'fbmp' ) === false ) {
			return;
		}
		wp_enqueue_style( 'fbmp-admin-style', FBMP_URL . 'assets/css/fbmp-admin.css', array(), FBMP_VERSION );
	}

	public static function register_menu() {
		add_menu_page( 'FB Member Portal', 'FB Member Portal', 'manage_options', 'fbmp-overview', array( __CLASS__, 'render_overview' ), 'dashicons-groups', 2 );
		add_submenu_page( 'fbmp-overview', 'Overview', 'Overview', 'manage_options', 'fbmp-overview', array( __CLASS__, 'render_overview' ) );
		add_submenu_page( 'fbmp-overview', 'Members', 'Members', 'manage_options', 'fbmp-members', array( __CLASS__, 'render_members' ) );
		add_submenu_page( 'fbmp-overview', 'Orders & Payments', 'Orders & Payments', 'manage_options', 'fbmp-orders', array( __CLASS__, 'render_orders' ) );
		add_submenu_page( 'fbmp-overview', 'Referrals', 'Referrals', 'manage_options', 'fbmp-referrals', array( __CLASS__, 'render_referrals' ) );
		add_submenu_page( 'fbmp-overview', 'Design Presets', 'Design Presets', 'manage_options', 'fbmp-presets', array( __CLASS__, 'render_presets' ) );
		add_submenu_page( 'fbmp-overview', 'Settings', 'Settings', 'manage_options', 'fbmp-settings', array( __CLASS__, 'render_settings' ) );
	}

	public static function handle_actions() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		if ( isset( $_POST['fbmp_change_role'], $_POST['fbmp_user_id'], $_POST['fbmp_new_role'] ) ) {
			check_admin_referer( 'fbmp_admin_action' );
			$user_id  = absint( $_POST['fbmp_user_id'] );
			$new_role = sanitize_text_field( wp_unslash( $_POST['fbmp_new_role'] ) );
			if ( in_array( $new_role, array( 'free_member', 'premium_member' ), true ) ) {
				$user = new WP_User( $user_id );
				$user->set_role( $new_role );
				add_action( 'admin_notices', function () {
					echo '<div class="notice notice-success is-dismissible"><p>Member role updated.</p></div>';
				} );
			}
		}
		if ( isset( $_POST['fbmp_add_order'] ) ) {
			check_admin_referer( 'fbmp_admin_action' );
			$user_id  = absint( $_POST['fbmp_order_user_id'] );
			$item     = sanitize_text_field( wp_unslash( $_POST['fbmp_order_item'] ) );
			$amount   = floatval( $_POST['fbmp_order_amount'] );
			$currency = sanitize_text_field( wp_unslash( $_POST['fbmp_order_currency'] ) );
			$status   = sanitize_text_field( wp_unslash( $_POST['fbmp_order_status'] ) );
			if ( $user_id && $item ) {
				FBMP_DB::insert_order( $user_id, $item, $amount, $currency, $status );
				add_action( 'admin_notices', function () {
					echo '<div class="notice notice-success is-dismissible"><p>Order added.</p></div>';
				} );
			}
		}
		if ( isset( $_POST['fbmp_save_settings'] ) ) {
			check_admin_referer( 'fbmp_admin_action' );
			update_option( 'fbmp_premium_category', sanitize_text_field( wp_unslash( $_POST['fbmp_premium_category'] ) ) );
			update_option( 'fbmp_stripe_secret_key', sanitize_text_field( wp_unslash( $_POST['fbmp_stripe_secret_key'] ) ) );
			update_option( 'fbmp_stripe_price_id', sanitize_text_field( wp_unslash( $_POST['fbmp_stripe_price_id'] ) ) );
			update_option( 'fbmp_stripe_webhook_secret', sanitize_text_field( wp_unslash( $_POST['fbmp_stripe_webhook_secret'] ) ) );
			update_option( 'fbmp_private_site', isset( $_POST['fbmp_private_site'] ) ? '1' : '0' );
			update_option( 'fbmp_sitewide_navbar', isset( $_POST['fbmp_sitewide_navbar'] ) ? sanitize_text_field( wp_unslash( $_POST['fbmp_sitewide_navbar'] ) ) : '' );
			update_option( 'fbmp_sitewide_footer', isset( $_POST['fbmp_sitewide_footer'] ) ? sanitize_text_field( wp_unslash( $_POST['fbmp_sitewide_footer'] ) ) : '' );
			update_option( 'fbmp_force_global_css', isset( $_POST['fbmp_force_global_css'] ) ? '1' : '0' );
			$redirect_type = isset( $_POST['fbmp_after_register_redirect'] ) ? sanitize_text_field( wp_unslash( $_POST['fbmp_after_register_redirect'] ) ) : 'dashboard';
			if ( ! in_array( $redirect_type, array( 'dashboard', 'custom' ), true ) ) { $redirect_type = 'dashboard'; }
			update_option( 'fbmp_after_register_redirect', $redirect_type );
			update_option( 'fbmp_after_register_custom_url', isset( $_POST['fbmp_after_register_custom_url'] ) ? esc_url_raw( wp_unslash( $_POST['fbmp_after_register_custom_url'] ) ) : '' );
			update_option( 'fbmp_send_welcome_email', isset( $_POST['fbmp_send_welcome_email'] ) ? '1' : '0' );
			update_option( 'fbmp_welcome_email_subject', isset( $_POST['fbmp_welcome_email_subject'] ) ? sanitize_text_field( wp_unslash( $_POST['fbmp_welcome_email_subject'] ) ) : '' );
			update_option( 'fbmp_welcome_email_body', isset( $_POST['fbmp_welcome_email_body'] ) ? sanitize_textarea_field( wp_unslash( $_POST['fbmp_welcome_email_body'] ) ) : '' );
			update_option( 'fbmp_notify_admin_on_register', isset( $_POST['fbmp_notify_admin_on_register'] ) ? '1' : '0' );
			update_option( 'fbmp_admin_notify_email', isset( $_POST['fbmp_admin_notify_email'] ) ? sanitize_email( wp_unslash( $_POST['fbmp_admin_notify_email'] ) ) : '' );
			add_action( 'admin_notices', function () {
				echo '<div class="notice notice-success is-dismissible"><p>Settings saved.</p></div>';
			} );
		}
	}

	public static function page_wrapper_start( $title ) {
		echo '<div class="wrap fbmp-wrap">';
		echo '<div class="fbmp-header"><span class="dashicons dashicons-groups"></span><h1>' . esc_html( $title ) . '</h1></div>';
	}

	public static function page_wrapper_end() {
		echo '</div>';
	}

	public static function tabs_nav( $active ) {
		$tabs = array(
			'fbmp-overview'  => 'Overview',
			'fbmp-members'   => 'Members',
			'fbmp-orders'    => 'Orders & Payments',
			'fbmp-referrals' => 'Referrals',
			'fbmp-presets'   => 'Design Presets',
			'fbmp-settings'  => 'Settings',
		);
		echo '<h2 class="nav-tab-wrapper fbmp-tabs">';
		foreach ( $tabs as $slug => $label ) {
			$class = ( $slug === $active ) ? 'nav-tab nav-tab-active' : 'nav-tab';
			printf( '<a href="%s" class="%s">%s</a>', esc_url( admin_url( 'admin.php?page=' . $slug ) ), esc_attr( $class ), esc_html( $label ) );
		}
		echo '</h2>';
	}

	public static function preset_preview_html( $render_callback ) {
		$body = call_user_func( $render_callback );
		return '<html><head><script src="https://cdn.tailwindcss.com"></script></head><body>' . $body . '</body></html>';
	}

	public static function render_overview() {
		require_once FBMP_PATH . 'includes/admin/overview.php';
		fbmp_admin_render_overview();
	}

	public static function render_members() {
		require_once FBMP_PATH . 'includes/admin/members.php';
		fbmp_admin_render_members();
	}

	public static function render_orders() {
		require_once FBMP_PATH . 'includes/admin/orders.php';
		fbmp_admin_render_orders();
	}

	public static function render_referrals() {
		require_once FBMP_PATH . 'includes/admin/referrals.php';
		fbmp_admin_render_referrals();
	}

	public static function render_presets() {
		require_once FBMP_PATH . 'includes/admin/presets.php';
		fbmp_admin_render_presets();
	}

	public static function render_settings() {
		require_once FBMP_PATH . 'includes/admin/settings.php';
		fbmp_admin_render_settings();
	}
}
