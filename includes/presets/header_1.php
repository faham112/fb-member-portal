<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Preset render: header_1
 */
function fbmp_preset_header_1() {
ob_start();
?>
<header class="text-center py-10 bg-gradient-to-b from-indigo-50 to-white">
	<h1 class="text-3xl font-bold text-gray-800"><?php bloginfo( 'name' ); ?></h1>
	<p class="text-gray-500 mt-2"><?php bloginfo( 'description' ); ?></p>
</header>
<?php
return ob_get_clean();
}
