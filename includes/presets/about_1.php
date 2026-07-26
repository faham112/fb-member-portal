<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Preset render: about_1
 */
function fbmp_preset_about_1() {
ob_start();
?>
<section class="max-w-5xl mx-auto px-4 py-16 grid md:grid-cols-2 gap-10 items-center">
	<div class="bg-gray-100 rounded-2xl h-64 flex items-center justify-center text-gray-400 text-sm">Image placeholder</div>
	<div>
		<h2 class="text-3xl font-bold text-gray-800 mb-4">About <?php bloginfo( 'name' ); ?></h2>
		<p class="text-gray-500 leading-relaxed"><?php bloginfo( 'description' ); ?> We built this platform to make membership simple — free to start, easy to upgrade, and built around a dashboard that actually gets out of your way.</p>
	</div>
</section>
<?php
return ob_get_clean();
}
