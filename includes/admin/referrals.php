<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin referrals page view.
 */
function fbmp_admin_render_referrals() {
FBMP_Admin::page_wrapper_start( 'Referrals' );
FBMP_Admin::tabs_nav( 'fbmp-referrals' );

$referrals = FBMP_Referral::get_all_referrals();
?>
<div class="fbmp-panel">
	<p class="description">This is a tracking report only — it does not calculate or pay commissions automatically. Use it to decide manual payouts yourself.</p>
	<table class="widefat striped">
		<thead>
			<tr>
				<th>Referred By</th>
				<th>New Member</th>
				<th>Joined</th>
			</tr>
		</thead>
		<tbody>
			<?php if ( empty( $referrals ) ) : ?>
				<tr><td colspan="3">No referrals tracked yet.</td></tr>
			<?php else : ?>
				<?php foreach ( $referrals as $row ) : ?>
					<tr>
						<td><?php echo esc_html( $row['referrer']->display_name ); ?></td>
						<td><?php echo esc_html( $row['referred']->display_name ); ?></td>
						<td><?php echo esc_html( date_i18n( 'M j, Y', strtotime( $row['referred']->user_registered ) ) ); ?></td>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
		</tbody>
	</table>
</div>
<?php
FBMP_Admin::page_wrapper_end();
}
