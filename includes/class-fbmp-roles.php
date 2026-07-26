<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FBMP_Roles {

	public static function init() {
		add_action( 'admin_init', array( __CLASS__, 'block_wp_admin' ) );
		add_action( 'admin_init', array( __CLASS__, 'repair_admin_role' ) );
		add_action( 'login_form_login', array( __CLASS__, 'redirect_wp_login' ) );
		add_filter( 'login_redirect', array( __CLASS__, 'role_based_redirect' ), 10, 3 );
		add_action( 'wp_logout', array( __CLASS__, 'redirect_after_logout' ) );
		add_filter( 'show_admin_bar', array( __CLASS__, 'hide_admin_bar_for_members' ) );
	}

	public static function repair_admin_role() {
		if ( ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$user         = wp_get_current_user();
		$member_roles = array( 'free_member', 'premium_member' );
		$stray        = array_intersect( $member_roles, (array) $user->roles );

		if ( $stray && in_array( 'administrator', (array) $user->roles, true ) ) {
			foreach ( $stray as $role ) {
				$user->remove_role( $role );
			}
		}
	}

	public static function hide_admin_bar_for_members( $show ) {
		if ( ! is_user_logged_in() ) {
			return $show;
		}

		if ( current_user_can( 'manage_options' ) ) {
			return $show;
		}

		$user         = wp_get_current_user();
		$member_roles = array( 'free_member', 'premium_member' );

		if ( array_intersect( $member_roles, (array) $user->roles ) ) {
			return false;
		}

		return $show;
	}

	public static function register_roles() {
		if ( ! get_role( 'free_member' ) ) {
			add_role(
				'free_member',
				__( 'Free Member', 'fbmp' ),
				array( 'read' => true )
			);
		}

		if ( ! get_role( 'premium_member' ) ) {
			add_role(
				'premium_member',
				__( 'Premium Member', 'fbmp' ),
				array(
					'read'               => true,
					'fbmp_premium_access' => true,
				)
			);
		}
	}

	public static function block_wp_admin() {
		if ( wp_doing_ajax() ) {
			return;
		}

		if ( ! is_user_logged_in() ) {
			return;
		}

		if ( current_user_can( 'manage_options' ) ) {
			return;
		}

		$user = wp_get_current_user();
		$member_roles = array( 'free_member', 'premium_member' );

		if ( array_intersect( $member_roles, (array) $user->roles ) ) {
			wp_safe_redirect( self::get_page_url( 'fbmp_dashboard_page_id' ) );
			exit;
		}
	}

	public static function redirect_wp_login() {
		if ( isset( $_GET['action'] ) && 'logout' === $_GET['action'] ) {
			return;
		}

		if ( ! empty( $_GET['reauth'] ) ) {
			return;
		}

		if ( is_user_logged_in() && current_user_can( 'manage_options' ) ) {
			wp_safe_redirect( admin_url() );
			exit;
		}
		wp_safe_redirect( self::get_page_url( 'fbmp_login_page_id' ) );
		exit;
	}

	public static function role_based_redirect( $redirect_to, $requested_redirect_to, $user ) {
		if ( isset( $user->roles ) && is_array( $user->roles ) ) {
			if ( user_can( $user, 'manage_options' ) ) {
				return admin_url();
			}
			$member_roles = array( 'free_member', 'premium_member' );
			if ( array_intersect( $member_roles, $user->roles ) ) {
				return self::get_page_url( 'fbmp_dashboard_page_id' );
			}
		}
		return $redirect_to;
	}

	public static function redirect_after_logout() {
		wp_safe_redirect( self::get_page_url( 'fbmp_login_page_id' ) );
		exit;
	}

	public static function maybe_create_pages() {
		$pages = array(
			'fbmp_login_page_id'     => array( 'title' => 'Login', 'content' => '[fbmp_login]' ),
			'fbmp_register_page_id'  => array( 'title' => 'Register', 'content' => '[fbmp_register]' ),
			'fbmp_dashboard_page_id' => array( 'title' => 'Dashboard', 'content' => '[fbmp_dashboard]' ),
		);

		foreach ( $pages as $option_key => $page ) {
			$existing_id = get_option( $option_key );
			if ( $existing_id && get_post( $existing_id ) ) {
				continue;
			}

			$page_id = wp_insert_post(
				array(
					'post_title'   => $page['title'],
					'post_content' => $page['content'],
					'post_status'  => 'publish',
					'post_type'    => 'page',
				)
			);

			if ( $page_id && ! is_wp_error( $page_id ) ) {
				update_option( $option_key, $page_id );
			}
		}
	}

	public static function get_page_url( $option_key ) {
		$page_id = get_option( $option_key );
		if ( $page_id ) {
			return get_permalink( $page_id );
		}
		return home_url( '/' );
	}

	public static function get_post_login_redirect_url() {
		if ( current_user_can( 'manage_options' ) ) {
			return admin_url();
		}
		return self::get_page_url( 'fbmp_dashboard_page_id' );
	}
}
