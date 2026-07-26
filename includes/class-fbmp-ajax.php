<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FBMP_Ajax {

	public static function init() {
		add_action( 'wp_ajax_nopriv_fbmp_login', array( __CLASS__, 'handle_login' ) );
		add_action( 'wp_ajax_nopriv_fbmp_register', array( __CLASS__, 'handle_register' ) );
		add_action( 'wp_ajax_fbmp_update_profile', array( __CLASS__, 'handle_profile_update' ) );
		add_action( 'wp_ajax_nopriv_fbmp_get_nonce', array( __CLASS__, 'handle_get_nonce' ) );
		add_action( 'wp_ajax_fbmp_get_nonce', array( __CLASS__, 'handle_get_nonce' ) );
	}

	public static function handle_get_nonce() {
		nocache_headers();
		wp_send_json_success( array( 'nonce' => wp_create_nonce( 'fbmp_nonce' ) ) );
	}

	private static function check_nonce() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'fbmp_nonce' ) ) {
			wp_send_json_error( array( 'message' => 'Security check failed. Please refresh and try again.' ) );
		}
	}

	public static function handle_login() {
		self::check_nonce();

		$username = isset( $_POST['username'] ) ? sanitize_text_field( wp_unslash( $_POST['username'] ) ) : '';
		$password = isset( $_POST['password'] ) ? $_POST['password'] : '';

		if ( empty( $username ) || empty( $password ) ) {
			wp_send_json_error( array( 'message' => 'Please fill in all fields.' ) );
		}

		$user = wp_signon(
			array(
				'user_login'    => $username,
				'user_password' => $password,
				'remember'      => true,
			),
			false
		);

		if ( is_wp_error( $user ) ) {
			wp_send_json_error( array( 'message' => 'Invalid username or password.' ) );
		}

		wp_set_current_user( $user->ID );
		wp_set_auth_cookie( $user->ID, true );

		if ( user_can( $user, 'manage_options' ) ) {
			$redirect = admin_url();
		} else {
			$member_roles = array( 'free_member', 'premium_member' );
			$is_member    = array_intersect( $member_roles, (array) $user->roles );
			$redirect     = $is_member ? FBMP_Roles::get_page_url( 'fbmp_dashboard_page_id' ) : admin_url();
		}

		wp_send_json_success( array( 'redirect' => $redirect ) );
	}

	public static function handle_register() {
		self::check_nonce();

		$full_name  = isset( $_POST['full_name'] ) ? sanitize_text_field( wp_unslash( $_POST['full_name'] ) ) : '';
		$username   = isset( $_POST['username'] ) ? sanitize_user( wp_unslash( $_POST['username'] ) ) : '';
		$email      = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
		$password   = isset( $_POST['password'] ) ? $_POST['password'] : '';
		$membership = isset( $_POST['membership'] ) ? sanitize_text_field( wp_unslash( $_POST['membership'] ) ) : 'free_member';

		if ( ! in_array( $membership, array( 'free_member', 'premium_member' ), true ) ) {
			$membership = 'free_member';
		}

		if ( empty( $full_name ) || empty( $username ) || empty( $email ) || empty( $password ) ) {
			wp_send_json_error( array( 'message' => 'Please fill in all fields.' ) );
		}

		if ( ! is_email( $email ) ) {
			wp_send_json_error( array( 'message' => 'Please enter a valid email address.' ) );
		}

		if ( username_exists( $username ) ) {
			wp_send_json_error( array( 'message' => 'That username is already taken.' ) );
		}

		if ( email_exists( $email ) ) {
			wp_send_json_error( array( 'message' => 'That email is already registered.' ) );
		}

		if ( strlen( $password ) < 6 ) {
			wp_send_json_error( array( 'message' => 'Password must be at least 6 characters.' ) );
		}

		$user_id = wp_insert_user(
			array(
				'user_login'   => $username,
				'user_email'   => $email,
				'user_pass'    => $password,
				'display_name' => $full_name,
				'first_name'   => $full_name,
				'role'         => $membership,
			)
		);

		if ( is_wp_error( $user_id ) ) {
			wp_send_json_error( array( 'message' => $user_id->get_error_message() ) );
		}

		FBMP_Referral::maybe_attach_referrer( $user_id );
		self::send_registration_emails( $user_id, $full_name, $username, $email );

		wp_set_current_user( $user_id );
		wp_set_auth_cookie( $user_id, true );

		$redirect_type = get_option( 'fbmp_after_register_redirect', 'dashboard' );
		if ( 'custom' === $redirect_type && get_option( 'fbmp_after_register_custom_url' ) ) {
			$redirect = get_option( 'fbmp_after_register_custom_url' );
		} else {
			$redirect = FBMP_Roles::get_page_url( 'fbmp_dashboard_page_id' );
		}

		wp_send_json_success( array( 'redirect' => $redirect ) );
	}

	private static function send_registration_emails( $user_id, $full_name, $username, $email ) {
		$login_url = FBMP_Roles::get_page_url( 'fbmp_login_page_id' );
		$site_name = get_bloginfo( 'name' );

		if ( '1' === get_option( 'fbmp_send_welcome_email', '1' ) ) {
			$subject = get_option( 'fbmp_welcome_email_subject', 'Welcome to {site_name}!' );
			$body    = get_option( 'fbmp_welcome_email_body', "Hi {name},\n\nYour account has been created successfully.\n\nUsername: {username}\nLogin here: {login_url}\n\nThanks,\n{site_name}" );

			$replacements = array(
				'{name}'       => $full_name,
				'{username}'   => $username,
				'{email}'      => $email,
				'{login_url}'  => $login_url,
				'{site_name}'  => $site_name,
			);

			$subject = strtr( $subject, $replacements );
			$body    = strtr( $body, $replacements );

			wp_mail( $email, $subject, $body );
		}

		if ( '1' === get_option( 'fbmp_notify_admin_on_register', '0' ) ) {
			$admin_email = get_option( 'fbmp_admin_notify_email', get_option( 'admin_email' ) );
			if ( $admin_email && is_email( $admin_email ) ) {
				wp_mail(
					$admin_email,
					'New registration on ' . $site_name,
					"A new user just registered:\n\nName: $full_name\nUsername: $username\nEmail: $email"
				);
			}
		}
	}

	public static function handle_profile_update() {
		self::check_nonce();

		if ( ! is_user_logged_in() ) {
			wp_send_json_error( array( 'message' => 'You must be logged in.' ) );
		}

		$user_id   = get_current_user_id();
		$full_name = isset( $_POST['full_name'] ) ? sanitize_text_field( wp_unslash( $_POST['full_name'] ) ) : '';
		$email     = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
		$password  = isset( $_POST['password'] ) ? $_POST['password'] : '';

		if ( empty( $full_name ) || empty( $email ) || ! is_email( $email ) ) {
			wp_send_json_error( array( 'message' => 'Please enter a valid name and email.' ) );
		}

		$existing_user_id = email_exists( $email );
		if ( $existing_user_id && (int) $existing_user_id !== (int) $user_id ) {
			wp_send_json_error( array( 'message' => 'That email is already in use by another account.' ) );
		}

		$update_data = array(
			'ID'           => $user_id,
			'display_name' => $full_name,
			'first_name'   => $full_name,
			'user_email'   => $email,
		);

		if ( ! empty( $password ) ) {
			if ( strlen( $password ) < 6 ) {
				wp_send_json_error( array( 'message' => 'Password must be at least 6 characters.' ) );
			}
			$update_data['user_pass'] = $password;
		}

		$result = wp_update_user( $update_data );

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( array( 'message' => $result->get_error_message() ) );
		}

		wp_send_json_success( array( 'message' => 'Profile updated successfully.' ) );
	}
}
