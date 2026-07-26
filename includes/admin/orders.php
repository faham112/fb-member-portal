<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin orders page view.
 */
function fbmp_admin_render_orders() {
FBMP_Admin::page_wrapper_start( 'Orders & Payments' );
FBMP_Admin::tabs_nav( 'fbmp-orders' );

global $wpdb;
$orders_table = FBMP_DB::orders_table();
$orders       = $wpdb->get_results( "SELECT * FROM $orders_table ORDER BY created_at DESC LIMIT 200" );
$members      = get_users( array( 'role__in' => array( 'free_member', 'premium_member' ) ) );
?>
<div class="fbmp-panel">
	<h2>Add Manual Order</h2>
	<form method="post" class="fbmp-inline-form">
		<?php wp_nonce_field( 'fbmp_admin_action' ); ?>
		<select name="fbmp_order_user_id" required>
			<option value="">Select member</option>
			<?php foreach ( $members as $m ) : ?>
				<option value="<?php echo esc_attr( $m->ID ); ?>"><?php echo esc_html( $m->display_name . ' (' . $m->user_email . ')' ); ?></option>
			<?php endforeach; ?>
		</select>
		<input type="text" name="fbmp_order_item" placeholder="Item / Plan name" required />
		<input type="number" step="0.01" name="fbmp_order_amount" placeholder="Amount" required />
		<input type="text" name="fbmp_order_currency" value="PKR" style="width:70px;" />
		<select name="fbmp_order_status">
			<option value="paid">Paid</option>
			<option value="pending">Pending</option>
			<option value="failed">Failed</option>
		</select>
		<button type="submit" name="fbmp_add_order" class="button button-primary">Add Order</button>
	</form>
</div>

<div class="fbmp-panel">
	<table class="widefat striped">
		<thead>
			<tr>
				<th>Member</th>
				<th>Item</th>
				<th>Amount</th>
				<th>Status</th>
				<th>Date</th>
			</tr>
		</thead>
		<tbody>
			<?php if ( empty( $orders ) ) : ?>
				<tr><td colspan="5">No orders yet.</td></tr>
			<?php else : ?>
				<?php foreach ( $orders as $o ) : ?>
					<?php $u = get_userdata( $o->user_id ); ?>
					<tr>
						<td><?php echo esc_html( $u ? $u->display_name : 'Unknown (#' . $o->user_id . ')' ); ?></td>
						<td><?php echo esc_html( $o->item ); ?></td>
						<td><?php echo esc_html( number_format( $o->amount, 2 ) . ' ' . $o->currency ); ?></td>
						<td>
							<span class="fbmp-badge <?php echo 'paid' === $o->status ? 'fbmp-badge-premium' : 'fbmp-badge-free'; ?>">
								<?php echo esc_html( ucfirst( $o->status ) ); ?>
							</span>
						</td>
						<td><?php echo esc_html( date_i18n( 'M j, Y', strtotime( $o->created_at ) ) ); ?></td>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
		</tbody>
	</table>
</div>
<?php
FBMP_Admin::page_wrapper_end();
}
