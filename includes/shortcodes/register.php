<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registration form shortcode view.
 */
function fbmp_render_register_form() {
if ( is_user_logged_in() ) {
	wp_safe_redirect( FBMP_Roles::get_post_login_redirect_url() );
	exit;
}
if ( ! headers_sent() ) {
	nocache_headers();
}
ob_start();
?>
<div class="max-w-md mx-auto mt-16 p-8 bg-white rounded-2xl shadow-lg">
	<h2 class="text-2xl font-bold mb-6 text-gray-800">Create Account</h2>
	<div id="fbmp-register-message" class="hidden mb-4 p-3 rounded-lg text-sm"></div>
	<form id="fbmp-register-form" class="space-y-4">
		<div>
			<label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
			<input type="text" name="full_name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" />
		</div>
		<div>
			<label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
			<input type="text" name="username" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" />
		</div>
		<div>
			<label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
			<input type="email" name="email" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" />
		</div>
		<div>
			<label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
			<input type="password" name="password" required minlength="6" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" />
		</div>
		<div>
			<label class="block text-sm font-medium text-gray-700 mb-1">Membership Type</label>
			<select name="membership" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
				<option value="free_member">Free</option>
				<option value="premium_member">Premium</option>
			</select>
			<p class="text-xs text-gray-500 mt-1">Premium selection here just tags the account — hook your payment step in before activating it for real.</p>
		</div>
		<button type="submit" class="w-full bg-indigo-600 text-white py-2 rounded-lg font-semibold hover:bg-indigo-700 transition">Sign Up</button>
	</form>
	<p class="mt-4 text-sm text-gray-600 text-center">
		Already have an account? <a href="<?php echo esc_url( FBMP_Roles::get_page_url( 'fbmp_login_page_id' ) ); ?>" class="text-indigo-600 font-medium">Login</a>
	</p>
</div>
<?php
return ob_get_clean();
}
