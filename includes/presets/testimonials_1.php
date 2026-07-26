<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Preset render: testimonials_1
 */
function fbmp_preset_testimonials_1() {
ob_start();
?>
<section class="max-w-6xl mx-auto px-4 py-16 bg-gray-50">
	<h2 class="text-3xl font-bold text-center text-gray-800 mb-10">What Members Say</h2>
	<div class="grid md:grid-cols-3 gap-6">
		<?php foreach ( array( 'Amara K.', 'Bilal R.', 'Sana T.' ) as $name ) : ?>
			<div class="bg-white rounded-2xl shadow p-6">
				<p class="text-gray-600 text-sm mb-4">"Signing up was quick and the dashboard makes everything easy to manage. Exactly what I needed."</p>
				<p class="font-semibold text-gray-800 text-sm">— <?php echo esc_html( $name ); ?></p>
			</div>
		<?php endforeach; ?>
	</div>
</section>
<?php
return ob_get_clean();
}
