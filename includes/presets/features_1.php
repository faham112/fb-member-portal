<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Preset render: features_1
 */
function fbmp_preset_features_1() {
ob_start();
?>
<section class="max-w-6xl mx-auto px-4 py-16">
	<h2 class="text-3xl font-bold text-center text-gray-800 mb-10">Why Members Love It Here</h2>
	<div class="grid md:grid-cols-3 gap-8">
		<div class="text-center">
			<div class="w-12 h-12 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center mx-auto mb-4 font-bold">1</div>
			<h3 class="font-semibold text-gray-800 mb-2">Instant Access</h3>
			<p class="text-sm text-gray-500">Sign up and get into your dashboard in seconds — no waiting.</p>
		</div>
		<div class="text-center">
			<div class="w-12 h-12 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center mx-auto mb-4 font-bold">2</div>
			<h3 class="font-semibold text-gray-800 mb-2">Premium Content</h3>
			<p class="text-sm text-gray-500">Unlock exclusive posts and resources with a simple upgrade.</p>
		</div>
		<div class="text-center">
			<div class="w-12 h-12 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center mx-auto mb-4 font-bold">3</div>
			<h3 class="font-semibold text-gray-800 mb-2">Refer & Track</h3>
			<p class="text-sm text-gray-500">Share your link and see who joined through you, right from your dashboard.</p>
		</div>
	</div>
</section>
<?php
return ob_get_clean();
}
