<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Preset render: cta_1
 */
function fbmp_preset_cta_1() {
ob_start();
?>
<section class="bg-indigo-600 py-16">
	<div class="max-w-3xl mx-auto px-4 text-center">
		<h2 class="text-3xl font-bold text-white mb-4">Ready to get started?</h2>
		<p class="text-indigo-100 mb-8">Join free today, upgrade to Premium whenever you're ready.</p>
		<a href="<?php echo esc_url( FBMP_Roles::get_page_url( 'fbmp_register_page_id' ) ); ?>" class="bg-white text-indigo-600 px-6 py-3 rounded-lg font-semibold hover:bg-gray-100 transition inline-block">Create Free Account</a>
	</div>
</section>
<?php
return ob_get_clean();
}
