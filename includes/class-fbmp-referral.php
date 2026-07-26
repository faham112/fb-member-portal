<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Referral tracking. This intentionally only TRACKS and REPORTS who
 * referred whom — it does not move money or auto-calculate/pay out
 * commissions. Any commission payout is a manual business decision the
 * site owner makes and records themselves (e.g. via a manual bank
 * transfer, tracked outside this plugin).
 */
class FBMP_Referral {

	const COOKIE_NAME = 'fbmp_ref';

	public static function init() {
		add_action( 'init', array( __CLASS__, 'capture_ref_param' ) );
	}

	/**
	 * If someone visits with ?ref=username, remember it in a short-lived
	 * cookie so we can credit the referrer if they register.
	 */
	public static function capture_ref_param() {
		if ( empty( $_GET['ref'] ) ) {
			return;
		}
		$ref = sanitize_user( wp_unslash( $_GET['ref'] ) );
		if ( $ref && username_exists( $ref ) && ! headers_sent() ) {
			setcookie( self::COOKIE_NAME, $ref, time() + ( 30 * DAY_IN_SECONDS ), COOKIEPATH, COOKIE_DOMAIN );
		}
	}

	/**
	 * Call this right after a new user registers to record who referred them.
	 */
	public static function maybe_attach_referrer( $new_user_id ) {
		if ( empty( $_COOKIE[ self::COOKIE_NAME ] ) ) {
			return;
		}
		$ref_username = sanitize_user( wp_unslash( $_COOKIE[ self::COOKIE_NAME ] ) );
		$referrer     = get_user_by( 'login', $ref_username );

		if ( $referrer && (int) $referrer->ID !== (int) $new_user_id ) {
			update_user_meta( $new_user_id, 'fbmp_referred_by', $referrer->ID );
		}
	}

	public static function get_referral_link( $user_id ) {
		$user = get_userdata( $user_id );
		if ( ! $user ) {
			return '';
		}
		return add_query_arg( 'ref', $user->user_login, FBMP_Roles::get_page_url( 'fbmp_register_page_id' ) );
	}

	/**
	 * Users this person referred (for their own dashboard / admin view).
	 */
	public static function get_referred_users( $referrer_id ) {
		return get_users(
			array(
				'meta_key'   => 'fbmp_referred_by',
				'meta_value' => $referrer_id,
				'fields'     => array( 'ID', 'display_name', 'user_email', 'user_registered' ),
			)
		);
	}

	/**
	 * All referral relationships, for the admin Referrals report.
	 */
	public static function get_all_referrals() {
		global $wpdb;
		$rows = $wpdb->get_results(
			"SELECT user_id, meta_value AS referrer_id FROM {$wpdb->usermeta} WHERE meta_key = 'fbmp_referred_by'"
		);

		$out = array();
		foreach ( $rows as $row ) {
			$referred = get_userdata( $row->user_id );
			$referrer = get_userdata( $row->referrer_id );
			if ( $referred && $referrer ) {
				$out[] = array(
					'referrer' => $referrer,
					'referred' => $referred,
				);
			}
		}
		return $out;
	}
}
