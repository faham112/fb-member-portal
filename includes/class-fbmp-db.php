<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FBMP_DB {

	public static function orders_table() {
		global $wpdb;
		return $wpdb->prefix . 'fbmp_orders';
	}

	public static function create_tables() {
		global $wpdb;
		$table_name      = self::orders_table();
		$charset_collate = $wpdb->get_charset_collate();

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$sql = "CREATE TABLE $table_name (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			user_id BIGINT UNSIGNED NOT NULL,
			item VARCHAR(191) NOT NULL,
			amount DECIMAL(10,2) NOT NULL DEFAULT 0,
			currency VARCHAR(10) NOT NULL DEFAULT 'USD',
			status VARCHAR(20) NOT NULL DEFAULT 'pending',
			created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			KEY user_id (user_id)
		) $charset_collate;";

		dbDelta( $sql );
	}

	public static function get_orders_for_user( $user_id ) {
		global $wpdb;
		$table = self::orders_table();
		return $wpdb->get_results(
			$wpdb->prepare( "SELECT * FROM $table WHERE user_id = %d ORDER BY created_at DESC", $user_id )
		);
	}

	public static function insert_order( $user_id, $item, $amount, $currency = 'USD', $status = 'pending' ) {
		global $wpdb;
		$wpdb->insert(
			self::orders_table(),
			array(
				'user_id'    => $user_id,
				'item'       => $item,
				'amount'     => $amount,
				'currency'   => $currency,
				'status'     => $status,
				'created_at' => current_time( 'mysql' ),
			),
			array( '%d', '%s', '%f', '%s', '%s', '%s' )
		);
		return $wpdb->insert_id;
	}
}
