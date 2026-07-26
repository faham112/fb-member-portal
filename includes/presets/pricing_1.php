<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Preset render: pricing_1
 */
function fbmp_preset_pricing_1() {
ob_start();
?>
<section id="pricing" class="max-w-4xl mx-auto px-4 py-16">
	<h2 class="text-3xl font-bold text-center text-gray-800 mb-10">Simple, Fair Pricing</h2>
	<div class="grid md:grid-cols-2 gap-6">
		<div class="border border-gray-200 rounded-2xl p-8">
			<h3 class="text-lg font-semibold text-gray-800">Free</h3>
			<p class="text-3xl font-bold mt-2 text-gray-900">$0</p>
			<ul class="mt-6 space-y-3 text-sm text-gray-600">
				<li>✓ Basic content access</li>
				<li>✓ Community dashboard</li>
				<li>✓ Referral link</li>
			</ul>
			<a href="<?php echo esc_url( FBMP_Roles::get_page_url( 'fbmp_register_page_id' ) ); ?>" class="mt-8 block text-center border border-gray-300 rounded-lg py-2 font-semibold hover:bg-gray-50 transition">Join Free</a>
		</div>
		<div class="border-2 border-indigo-600 rounded-2xl p-8 relative">
			<span class="absolute -top-3 left-8 bg-indigo-600 text-white text-xs font-semibold px-3 py-1 rounded-full">Popular</span>
			<h3 class="text-lg font-semibold text-gray-800">Premium</h3>
			<p class="text-3xl font-bold mt-2 text-gray-900">Paid<span class="text-base font-normal text-gray-500">/month</span></p>
			<ul class="mt-6 space-y-3 text-sm text-gray-600">
				<li>✓ Everything in Free</li>
				<li>✓ Premium-only content</li>
				<li>✓ Priority support</li>
			</ul>
			<a href="<?php echo esc_url( FBMP_Roles::get_page_url( 'fbmp_register_page_id' ) ); ?>" class="mt-8 block text-center bg-indigo-600 text-white rounded-lg py-2 font-semibold hover:bg-indigo-700 transition">Upgrade to Premium</a>
		</div>
	</div>
</section>
<?php
return ob_get_clean();
}
