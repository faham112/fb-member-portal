<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Preset render: faq_1
 */
function fbmp_preset_faq_1() {
ob_start();
?>
<section class="max-w-3xl mx-auto px-4 py-16">
	<h2 class="text-3xl font-bold text-center text-gray-800 mb-10">Frequently Asked Questions</h2>
	<div class="space-y-4">
		<?php
		$faqs = array(
			'How do I upgrade to Premium?' => 'Just click "Upgrade to Premium" on your dashboard — it opens a secure Stripe checkout.',
			'Can I cancel anytime?'         => 'Yes, Premium is a standard recurring subscription with no long-term lock-in.',
			'How does the referral link work?' => 'Share your personal link — anyone who signs up through it shows up in your Referrals tab.',
		);
		foreach ( $faqs as $q => $a ) :
			?>
			<details class="border border-gray-200 rounded-xl p-4">
				<summary class="font-semibold text-gray-800 cursor-pointer"><?php echo esc_html( $q ); ?></summary>
				<p class="text-sm text-gray-500 mt-2"><?php echo esc_html( $a ); ?></p>
			</details>
		<?php endforeach; ?>
	</div>
</section>
<?php
return ob_get_clean();
}
