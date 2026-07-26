<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Preset render: footer_1
 */
function fbmp_preset_footer_1() {
ob_start();
?>
<footer class="bg-gray-900 text-gray-300 mt-16">
	<div class="max-w-6xl mx-auto px-4 py-12 grid md:grid-cols-3 gap-8">
		<div>
			<h4 class="text-white font-semibold mb-3"><?php bloginfo( 'name' ); ?></h4>
			<p class="text-sm text-gray-400"><?php bloginfo( 'description' ); ?></p>
		</div>
		<div>
			<h4 class="text-white font-semibold mb-3">Links</h4>
			<ul class="space-y-2 text-sm">
				<li><a href="<?php echo esc_url( FBMP_Roles::get_page_url( 'fbmp_login_page_id' ) ); ?>" class="hover:text-white">Login</a></li>
				<li><a href="<?php echo esc_url( FBMP_Roles::get_page_url( 'fbmp_register_page_id' ) ); ?>" class="hover:text-white">Register</a></li>
				<li><a href="#pricing" class="hover:text-white">Pricing</a></li>
			</ul>
		</div>
		<div>
			<h4 class="text-white font-semibold mb-3">Follow</h4>
			<div class="flex gap-3 text-sm">
				<a href="#" class="hover:text-white">Facebook</a>
				<a href="#" class="hover:text-white">Instagram</a>
				<a href="#" class="hover:text-white">X</a>
			</div>
		</div>
	</div>
	<div class="border-t border-gray-800 text-center text-xs text-gray-500 py-4">
		&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. Code by Faham Baloch.
	</div>
</footer>
<?php
return ob_get_clean();
}
