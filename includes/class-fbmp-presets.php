<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Design presets library + site-wide navbar/footer + insert automation.
 */
class FBMP_Presets {

	public static function init() {
		add_shortcode( 'fbmp_preset', array( __CLASS__, 'render_preset_shortcode' ) );
		add_action( 'wp_ajax_fbmp_insert_preset', array( __CLASS__, 'handle_insert_preset' ) );
		add_action( 'wp_body_open', array( __CLASS__, 'output_sitewide_navbar' ) );
		add_action( 'wp_footer', array( __CLASS__, 'output_sitewide_footer' ) );
	}

	public static function output_sitewide_navbar() {
		$key = get_option( 'fbmp_sitewide_navbar', '' );
		if ( empty( $key ) ) {
			return;
		}
		$presets = self::get_presets();
		if ( ! empty( $presets[ $key ] ) ) {
			echo call_user_func( $presets[ $key ]['render'] ); // phpcs:ignore WordPress.Security.EscapeOutput
		}
	}

	public static function output_sitewide_footer() {
		$key = get_option( 'fbmp_sitewide_footer', '' );
		if ( empty( $key ) ) {
			return;
		}
		$presets = self::get_presets();
		if ( ! empty( $presets[ $key ] ) ) {
			echo call_user_func( $presets[ $key ]['render'] ); // phpcs:ignore WordPress.Security.EscapeOutput
		}
	}

	public static function handle_insert_preset() {
		check_ajax_referer( 'fbmp_admin_action', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => 'Permission denied.' ) );
		}

		$key      = isset( $_POST['key'] ) ? sanitize_text_field( wp_unslash( $_POST['key'] ) ) : '';
		$page_id  = isset( $_POST['page_id'] ) ? absint( $_POST['page_id'] ) : 0;
		$position = isset( $_POST['position'] ) ? sanitize_text_field( wp_unslash( $_POST['position'] ) ) : 'append';

		$presets = self::get_presets();
		if ( empty( $presets[ $key ] ) ) {
			wp_send_json_error( array( 'message' => 'Unknown preset.' ) );
		}

		$page = get_post( $page_id );
		if ( ! $page || ! in_array( $page->post_type, array( 'page', 'post' ), true ) ) {
			wp_send_json_error( array( 'message' => 'Please choose a valid page.' ) );
		}

		$elementor_mode = get_post_meta( $page_id, '_elementor_edit_mode', true );
		if ( 'builder' === $elementor_mode ) {
			wp_send_json_error(
				array(
					'message' => sprintf(
						'"%s" is built with Elementor — Insert won\'t show up there. Open it in Elementor, drag the "Shortcode" widget onto the page, and paste: [fbmp_preset key="%s"]',
						get_the_title( $page_id ),
						$key
					),
				)
			);
		}

		$shortcode = '[fbmp_preset key="' . $key . '"]';
		$separator = "\n\n";

		if ( 'prepend' === $position ) {
			$new_content = $shortcode . $separator . $page->post_content;
		} else {
			$new_content = $page->post_content . $separator . $shortcode;
		}

		$updated = wp_update_post(
			array(
				'ID'           => $page_id,
				'post_content' => $new_content,
			),
			true
		);

		if ( is_wp_error( $updated ) ) {
			wp_send_json_error( array( 'message' => $updated->get_error_message() ) );
		}

		wp_send_json_success(
			array(
				'message'   => sprintf( '"%s" inserted into "%s".', $presets[ $key ]['label'], get_the_title( $page_id ) ),
				'view_url'  => get_permalink( $page_id ),
				'edit_url'  => get_edit_post_link( $page_id, 'raw' ),
			)
		);
	}

	public static function get_presets() {
		static $loaded = false;
		if ( ! $loaded ) {
			$files = array( 'navbar_1', 'header_1', 'hero_1', 'features_1', 'testimonials_1', 'pricing_1', 'faq_1', 'about_1', 'cta_1', 'footer_1' );
			foreach ( $files as $f ) {
				require_once FBMP_PATH . 'includes/presets/' . $f . '.php';
			}
			$loaded = true;
		}

		return array(
			'navbar-1' => array( 'label' => 'Navbar — Simple with CTA', 'category' => 'Navbar', 'render' => 'fbmp_preset_navbar_1' ),
			'header-1' => array( 'label' => 'Header — Logo + Tagline', 'category' => 'Header', 'render' => 'fbmp_preset_header_1' ),
			'hero-1' => array( 'label' => 'Hero Section — Headline + CTA', 'category' => 'Hero', 'render' => 'fbmp_preset_hero_1' ),
			'features-1' => array( 'label' => 'Features — 3 Column Grid', 'category' => 'Features', 'render' => 'fbmp_preset_features_1' ),
			'testimonials-1' => array( 'label' => 'Testimonials — 3 Cards', 'category' => 'Testimonials', 'render' => 'fbmp_preset_testimonials_1' ),
			'pricing-1' => array( 'label' => 'Pricing — Free vs Premium', 'category' => 'Pricing', 'render' => 'fbmp_preset_pricing_1' ),
			'faq-1' => array( 'label' => 'FAQ — Accordion Style', 'category' => 'FAQ', 'render' => 'fbmp_preset_faq_1' ),
			'about-1' => array( 'label' => 'About — Image + Text', 'category' => 'About', 'render' => 'fbmp_preset_about_1' ),
			'cta-1' => array( 'label' => 'CTA Banner — Full Width', 'category' => 'Call To Action', 'render' => 'fbmp_preset_cta_1' ),
			'footer-1' => array( 'label' => 'Footer — Columns + Social', 'category' => 'Footer', 'render' => 'fbmp_preset_footer_1' ),
		);
	}

	public static function render_preset_shortcode( $atts ) {
		$atts    = shortcode_atts( array( 'key' => '' ), $atts, 'fbmp_preset' );
		$presets = self::get_presets();

		if ( empty( $atts['key'] ) || empty( $presets[ $atts['key'] ] ) ) {
			return '';
		}

		return call_user_func( $presets[ $atts['key'] ]['render'] );
	}
}
