<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Real Stripe subscription checkout for the Premium tier. This charges
 * the user's card via Stripe and, once Stripe confirms payment, upgrades
 * their WordPress role to premium_member. No investment return of any
 * kind is calculated or promised — this is a standard paid-membership
 * subscription, same as any SaaS "Pro plan".
 */
class FBMP_Stripe {

	public static function init() {
		add_action( 'wp_ajax_fbmp_create_checkout', array( __CLASS__, 'create_checkout_session' ) );
		add_action( 'init', array( __CLASS__, 'register_webhook_endpoint' ) );
	}

	private static function secret_key() {
		return get_option( 'fbmp_stripe_secret_key', '' );
	}

	private static function price_id() {
		return get_option( 'fbmp_stripe_price_id', '' );
	}

	/**
	 * Logged-in member clicks "Upgrade to Premium" -> we create a Stripe
	 * Checkout Session and hand the URL back to redirect them to Stripe's
	 * hosted payment page. No card details ever touch this server.
	 */
	public static function create_checkout_session() {
		check_ajax_referer( 'fbmp_nonce', 'nonce' );

		if ( ! is_user_logged_in() ) {
			wp_send_json_error( array( 'message' => 'Please log in first.' ) );
		}

		$secret_key = self::secret_key();
		$price_id   = self::price_id();

		if ( empty( $secret_key ) || empty( $price_id ) ) {
			wp_send_json_error( array( 'message' => 'Stripe is not configured yet. Ask the site admin to add API keys in Settings.' ) );
		}

		$user           = wp_get_current_user();
		$dashboard_url  = FBMP_Roles::get_page_url( 'fbmp_dashboard_page_id' );

		$response = wp_remote_post(
			'https://api.stripe.com/v1/checkout/sessions',
			array(
				'headers' => array(
					'Authorization' => 'Bearer ' . $secret_key,
					'Content-Type'  => 'application/x-www-form-urlencoded',
				),
				'body'    => array(
					'mode'                 => 'subscription',
					'line_items'           => array(
						array(
							'price'    => $price_id,
							'quantity' => 1,
						),
					),
					'customer_email'       => $user->user_email,
					'client_reference_id'  => $user->ID,
					'success_url'          => add_query_arg( 'fbmp_upgrade', 'success', $dashboard_url ),
					'cancel_url'           => add_query_arg( 'fbmp_upgrade', 'cancelled', $dashboard_url ),
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			wp_send_json_error( array( 'message' => 'Could not reach Stripe. Try again shortly.' ) );
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( empty( $body['url'] ) ) {
			$error_message = isset( $body['error']['message'] ) ? $body['error']['message'] : 'Stripe checkout could not be created.';
			wp_send_json_error( array( 'message' => $error_message ) );
		}

		wp_send_json_success( array( 'checkout_url' => $body['url'] ) );
	}

	/**
	 * A lightweight webhook endpoint at yoursite.com/?fbmp_stripe_webhook=1
	 * Stripe calls this after a successful subscription payment; we verify
	 * the signature, then upgrade the paying user's role and log the order.
	 */
	public static function register_webhook_endpoint() {
		if ( empty( $_GET['fbmp_stripe_webhook'] ) ) {
			return;
		}

		$payload    = @file_get_contents( 'php://input' );
		$sig_header = isset( $_SERVER['HTTP_STRIPE_SIGNATURE'] ) ? $_SERVER['HTTP_STRIPE_SIGNATURE'] : '';
		$secret     = get_option( 'fbmp_stripe_webhook_secret', '' );

		if ( empty( $secret ) || ! self::verify_signature( $payload, $sig_header, $secret ) ) {
			status_header( 400 );
			exit( 'Invalid signature' );
		}

		$event = json_decode( $payload, true );

		if ( isset( $event['type'] ) && 'checkout.session.completed' === $event['type'] ) {
			$session = $event['data']['object'];
			$user_id = isset( $session['client_reference_id'] ) ? absint( $session['client_reference_id'] ) : 0;
			$amount  = isset( $session['amount_total'] ) ? $session['amount_total'] / 100 : 0;
			$currency = isset( $session['currency'] ) ? strtoupper( $session['currency'] ) : 'USD';

			if ( $user_id ) {
				$user = new WP_User( $user_id );
				$user->set_role( 'premium_member' );
				FBMP_DB::insert_order( $user_id, 'Premium Subscription', $amount, $currency, 'paid' );
			}
		}

		status_header( 200 );
		exit( 'ok' );
	}

	/**
	 * Minimal Stripe webhook signature verification (no SDK dependency).
	 */
	private static function verify_signature( $payload, $sig_header, $secret ) {
		if ( empty( $sig_header ) ) {
			return false;
		}

		$parts = array();
		foreach ( explode( ',', $sig_header ) as $part ) {
			list( $key, $value ) = array_pad( explode( '=', $part, 2 ), 2, '' );
			$parts[ $key ] = $value;
		}

		if ( empty( $parts['t'] ) || empty( $parts['v1'] ) ) {
			return false;
		}

		$signed_payload     = $parts['t'] . '.' . $payload;
		$expected_signature = hash_hmac( 'sha256', $signed_payload, $secret );

		return hash_equals( $expected_signature, $parts['v1'] );
	}
}
