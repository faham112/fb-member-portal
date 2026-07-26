<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Preset render: navbar_1
 */
function fbmp_preset_navbar_1() {
$logged_in  = is_user_logged_in();
$user       = $logged_in ? wp_get_current_user() : null;
$is_premium = $logged_in && in_array( 'premium_member', (array) $user->roles, true );

$home_url      = home_url( '/' );
$about_url     = home_url( '/about-us' );
$dashboard_url = FBMP_Roles::get_page_url( 'fbmp_dashboard_page_id' );
$login_url     = FBMP_Roles::get_page_url( 'fbmp_login_page_id' );
$logout_url    = wp_logout_url();

ob_start();
?>
<nav class="bg-white shadow-sm relative">
	<div class="max-w-6xl mx-auto px-4 flex items-center justify-between h-16">
		<a href="<?php echo esc_url( $home_url ); ?>" class="text-xl font-bold text-indigo-600"><?php bloginfo( 'name' ); ?></a>

		<!-- Desktop menu (md and up) -->
		<div class="hidden md:flex items-center gap-8 text-sm font-medium text-gray-600">
			<?php if ( ! $logged_in ) : ?>
				<a href="<?php echo esc_url( $home_url ); ?>" class="hover:text-indigo-600">Home</a>
				<a href="<?php echo esc_url( $about_url ); ?>" class="hover:text-indigo-600">About Us</a>
				<a href="#pricing" class="hover:text-indigo-600">Pricing</a>
			<?php else : ?>
				<a href="<?php echo esc_url( $dashboard_url ); ?>" class="hover:text-indigo-600">Dashboard</a>
				<a href="<?php echo esc_url( $home_url ); ?>" class="hover:text-indigo-600">Home</a>
			<?php endif; ?>
		</div>

		<div class="hidden md:block">
			<?php if ( ! $logged_in ) : ?>
				<a href="<?php echo esc_url( $login_url ); ?>" class="bg-indigo-600 text-white text-sm font-semibold px-4 py-2 rounded-lg hover:bg-indigo-700 transition">Login</a>
			<?php else : ?>
				<details class="relative">
					<summary class="list-none flex items-center gap-2 cursor-pointer select-none">
						<span class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-sm font-semibold">
							<?php echo esc_html( strtoupper( substr( $user->display_name, 0, 1 ) ) ); ?>
						</span>
						<span class="text-sm font-medium text-gray-700"><?php echo esc_html( $user->display_name ); ?></span>
						<span class="text-[10px] font-semibold px-2 py-0.5 rounded-full <?php echo $is_premium ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-500'; ?>">
							<?php echo $is_premium ? 'Premium' : 'Free'; ?>
						</span>
					</summary>
					<div class="absolute right-0 mt-2 w-44 bg-white border border-gray-200 rounded-xl shadow-lg py-2 text-sm z-20">
						<a href="<?php echo esc_url( $dashboard_url ); ?>" class="block px-4 py-2 text-gray-700 hover:bg-gray-50">Dashboard</a>
						<a href="<?php echo esc_url( $logout_url ); ?>" class="block px-4 py-2 text-red-600 hover:bg-gray-50">Log out</a>
					</div>
				</details>
			<?php endif; ?>
		</div>

		<!-- Mobile hamburger (below md) -->
		<details class="md:hidden">
			<summary class="list-none cursor-pointer p-2 -mr-2">
				<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
					<path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
				</svg>
			</summary>

			<div class="absolute left-0 right-0 top-16 bg-white border-t border-gray-100 shadow-lg z-30 px-4 py-4 space-y-1 text-sm font-medium text-gray-700">
				<?php if ( ! $logged_in ) : ?>
					<a href="<?php echo esc_url( $home_url ); ?>" class="block py-2 hover:text-indigo-600">Home</a>
					<a href="<?php echo esc_url( $about_url ); ?>" class="block py-2 hover:text-indigo-600">About Us</a>
					<a href="#pricing" class="block py-2 hover:text-indigo-600">Pricing</a>
					<a href="<?php echo esc_url( $login_url ); ?>" class="block mt-2 bg-indigo-600 text-white text-center font-semibold px-4 py-2 rounded-lg hover:bg-indigo-700 transition">Login</a>
				<?php else : ?>
					<div class="flex items-center gap-2 pb-2 mb-2 border-b border-gray-100">
						<span class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-sm font-semibold">
							<?php echo esc_html( strtoupper( substr( $user->display_name, 0, 1 ) ) ); ?>
						</span>
						<span class="text-sm font-medium text-gray-700"><?php echo esc_html( $user->display_name ); ?></span>
						<span class="text-[10px] font-semibold px-2 py-0.5 rounded-full <?php echo $is_premium ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-500'; ?>">
							<?php echo $is_premium ? 'Premium' : 'Free'; ?>
						</span>
					</div>
					<a href="<?php echo esc_url( $dashboard_url ); ?>" class="block py-2 hover:text-indigo-600">Dashboard</a>
					<a href="<?php echo esc_url( $home_url ); ?>" class="block py-2 hover:text-indigo-600">Home</a>
					<a href="<?php echo esc_url( $logout_url ); ?>" class="block py-2 text-red-600">Log out</a>
				<?php endif; ?>
			</div>
		</details>
	</div>
</nav>
<?php
return ob_get_clean();
}
