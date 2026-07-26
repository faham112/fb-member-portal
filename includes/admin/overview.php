<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin overview page view.
 */
function fbmp_admin_render_overview() {
FBMP_Admin::page_wrapper_start( 'FB Member Portal' );
FBMP_Admin::tabs_nav( 'fbmp-overview' );

$free_count    = count( get_users( array( 'role' => 'free_member', 'fields' => 'ID' ) ) );
$premium_count = count( get_users( array( 'role' => 'premium_member', 'fields' => 'ID' ) ) );

global $wpdb;
$orders_table  = FBMP_DB::orders_table();
$total_orders  = (int) $wpdb->get_var( "SELECT COUNT(*) FROM $orders_table" );
$total_revenue = (float) $wpdb->get_var( "SELECT SUM(amount) FROM $orders_table WHERE status = 'paid'" );

?>
<div class="fbmp-cards">
	<div class="fbmp-card">
		<span class="fbmp-card-label">Free Members</span>
		<span class="fbmp-card-value"><?php echo esc_html( $free_count ); ?></span>
	</div>
	<div class="fbmp-card">
		<span class="fbmp-card-label">Premium Members</span>
		<span class="fbmp-card-value"><?php echo esc_html( $premium_count ); ?></span>
	</div>
	<div class="fbmp-card">
		<span class="fbmp-card-label">Total Orders</span>
		<span class="fbmp-card-value"><?php echo esc_html( $total_orders ); ?></span>
	</div>
	<div class="fbmp-card">
		<span class="fbmp-card-label">Revenue (paid)</span>
		<span class="fbmp-card-value"><?php echo esc_html( number_format( $total_revenue, 2 ) ); ?></span>
	</div>
</div>

<div class="fbmp-panel">
	<h2>Quick Links</h2>
	<p>
		<a href="<?php echo esc_url( FBMP_Roles::get_page_url( 'fbmp_login_page_id' ) ); ?>" class="button" target="_blank">View Login Page</a>
		<a href="<?php echo esc_url( FBMP_Roles::get_page_url( 'fbmp_register_page_id' ) ); ?>" class="button" target="_blank">View Register Page</a>
		<a href="<?php echo esc_url( FBMP_Roles::get_page_url( 'fbmp_dashboard_page_id' ) ); ?>" class="button" target="_blank">View Dashboard Page</a>
	</p>
	<p class="description">Built and coded by Faham Baloch.</p>
</div>
<?php
FBMP_Admin::page_wrapper_end();
}
