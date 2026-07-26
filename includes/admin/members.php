<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin members page view.
 */
function fbmp_admin_render_members() {
FBMP_Admin::page_wrapper_start( 'Members' );
FBMP_Admin::tabs_nav( 'fbmp-members' );

$members = get_users( array( 'role__in' => array( 'free_member', 'premium_member' ), 'orderby' => 'registered', 'order' => 'DESC' ) );
?>
<div class="fbmp-panel">
	<table class="widefat striped">
		<thead>
			<tr>
				<th>Name</th>
				<th>Email</th>
				<th>Role</th>
				<th>Joined</th>
				<th>Change Role</th>
			</tr>
		</thead>
		<tbody>
			<?php if ( empty( $members ) ) : ?>
				<tr><td colspan="5">No members yet.</td></tr>
			<?php else : ?>
				<?php foreach ( $members as $m ) : ?>
					<?php $is_premium = in_array( 'premium_member', (array) $m->roles, true ); ?>
					<tr>
						<td><?php echo esc_html( $m->display_name ); ?></td>
						<td><?php echo esc_html( $m->user_email ); ?></td>
						<td>
							<span class="fbmp-badge <?php echo $is_premium ? 'fbmp-badge-premium' : 'fbmp-badge-free'; ?>">
								<?php echo $is_premium ? 'Premium' : 'Free'; ?>
							</span>
						</td>
						<td><?php echo esc_html( date_i18n( 'M j, Y', strtotime( $m->user_registered ) ) ); ?></td>
						<td>
							<form method="post" style="display:inline-flex; gap:6px;">
								<?php wp_nonce_field( 'fbmp_admin_action' ); ?>
								<input type="hidden" name="fbmp_user_id" value="<?php echo esc_attr( $m->ID ); ?>" />
								<input type="hidden" name="fbmp_new_role" value="<?php echo $is_premium ? 'free_member' : 'premium_member'; ?>" />
								<button type="submit" name="fbmp_change_role" class="button button-small">
									<?php echo $is_premium ? 'Downgrade to Free' : 'Upgrade to Premium'; ?>
								</button>
							</form>
						</td>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
		</tbody>
	</table>
</div>
<?php
FBMP_Admin::page_wrapper_end();
}
