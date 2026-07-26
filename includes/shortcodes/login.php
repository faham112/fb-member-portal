<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Login form shortcode view.
 */
function fbmp_render_login_form() {
if ( is_user_logged_in() ) {
	wp_safe_redirect( FBMP_Roles::get_post_login_redirect_url() );
	exit;
}
if ( ! headers_sent() ) {
	nocache_headers(); // discourage proxy/CDN caching of this page
}
ob_start();
?>
<div class="max-w-md mx-auto mt-16 p-8 bg-white rounded-2xl shadow-lg">
	<h2 class="text-2xl font-bold mb-6 text-gray-800">Login</h2>
	<div id="fbmp-login-message" class="hidden mb-4 p-3 rounded-lg text-sm"></div>
	<form id="fbmp-login-form" class="space-y-4">
		<div>
			<label class="block text-sm font-medium text-gray-700 mb-1">Username or Email</label>
			<input type="text" name="username" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" />
		</div>
		<div>
			<label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
			<input type="password" name="password" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" />
		</div>
		<button type="submit" class="w-full bg-indigo-600 text-white py-2 rounded-lg font-semibold hover:bg-indigo-700 transition">Log In</button>
	</form>
	<p class="mt-4 text-sm text-gray-600 text-center">
		No account? <a href="<?php echo esc_url( FBMP_Roles::get_page_url( 'fbmp_register_page_id' ) ); ?>" class="text-indigo-600 font-medium">Register</a>
	</p>
	<p class="mt-2 text-xs text-gray-400 text-center">
		<a href="#" id="fbmp-rescue-toggle" class="hover:underline">Lost Administrator access?</a>
	</p>

	<div id="fbmp-rescue-box" class="hidden mt-4 pt-4 border-t border-gray-100">
		<div id="fbmp-rescue-message" class="hidden mb-3 p-3 rounded-lg text-sm"></div>
		<form id="fbmp-rescue-form" class="space-y-3">
			<label class="block text-sm font-medium text-gray-700">Enter your account email — we'll send a secure restore link.</label>
			<input type="email" name="email" required class="w-full px-4 py-2 border border-gray-300 rounded-lg" />
			<button type="submit" class="w-full bg-gray-700 text-white py-2 rounded-lg font-semibold hover:bg-gray-800 transition">Send Restore Link</button>
		</form>
	</div>
</div>
<?php
return ob_get_clean();
}
