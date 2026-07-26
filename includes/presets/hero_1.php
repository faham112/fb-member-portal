<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Preset render: hero_1
 */
function fbmp_preset_hero_1() {
ob_start();
?>
<section class="max-w-5xl mx-auto px-4 py-20 text-center">
	<h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 leading-tight">Grow faster with a membership built for you</h1>
	<p class="mt-4 text-lg text-gray-500 max-w-2xl mx-auto">Join free, upgrade whenever you're ready — no long-term commitment, cancel anytime.</p>
	<div class="mt-8 flex justify-center gap-4">
		<a href="<?php echo esc_url( FBMP_Roles::get_page_url( 'fbmp_register_page_id' ) ); ?>" class="bg-indigo-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-indigo-700 transition">Get Started Free</a>
		<a href="#pricing" class="border border-gray-300 text-gray-700 px-6 py-3 rounded-lg font-semibold hover:bg-gray-50 transition">See Pricing</a>
	</div>
</section>
<?php
return ob_get_clean();
}
