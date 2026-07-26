jQuery(function ($) {
	'use strict';

	function showMessage($el, message, isError) {
		$el
			.removeClass('hidden bg-red-50 text-red-700 bg-green-50 text-green-700')
			.addClass(isError ? 'bg-red-50 text-red-700' : 'bg-green-50 text-green-700')
			.text(message);
	}

	function postAjax(action, data, $msg, onSuccess) {
		// Always grab a brand-new nonce first — admin-ajax.php is dynamic
		// and never CDN/page-cached, so this sidesteps "Security check
		// failed" errors caused by a stale nonce baked into a cached page.
		$.post(FBMP.ajax_url, { action: 'fbmp_get_nonce' })
			.done(function (nonceRes) {
				const freshNonce = (nonceRes.success && nonceRes.data.nonce) || FBMP.nonce;
				$.post(FBMP.ajax_url, Object.assign({ action: action, nonce: freshNonce }, data))
					.done(function (res) {
						if (res.success) {
							if (onSuccess) onSuccess(res.data);
						} else {
							showMessage($msg, (res.data && res.data.message) || 'Something went wrong.', true);
						}
					})
					.fail(function () {
						showMessage($msg, 'Network error. Please try again.', true);
					});
			})
			.fail(function () {
				showMessage($msg, 'Network error. Please try again.', true);
			});
	}

	// Login form
	$('#fbmp-login-form').on('submit', function (e) {
		e.preventDefault();
		const $msg = $('#fbmp-login-message');
		const data = {
			username: $(this).find('[name=username]').val(),
			password: $(this).find('[name=password]').val(),
		};
		postAjax('fbmp_login', data, $msg, function (result) {
			showMessage($msg, 'Logged in! Redirecting...', false);
			window.location.href = result.redirect;
		});
	});

	// Register form
	$('#fbmp-register-form').on('submit', function (e) {
		e.preventDefault();
		const $msg = $('#fbmp-register-message');
		const data = {
			full_name: $(this).find('[name=full_name]').val(),
			username: $(this).find('[name=username]').val(),
			email: $(this).find('[name=email]').val(),
			password: $(this).find('[name=password]').val(),
			membership: $(this).find('[name=membership]').val(),
		};
		postAjax('fbmp_register', data, $msg, function (result) {
			showMessage($msg, 'Account created! Redirecting...', false);
			window.location.href = result.redirect;
		});
	});

	// Profile update form
	$('#fbmp-profile-form').on('submit', function (e) {
		e.preventDefault();
		const $msg = $('#fbmp-profile-message');
		const data = {
			full_name: $(this).find('[name=full_name]').val(),
			email: $(this).find('[name=email]').val(),
			password: $(this).find('[name=password]').val(),
		};
		postAjax('fbmp_update_profile', data, $msg, function (result) {
			showMessage($msg, result.message, false);
		});
	});

	// Dashboard tabs
	$('.fbmp-tab-btn').on('click', function () {
		const tab = $(this).data('tab');

		$('.fbmp-tab-btn').removeClass('text-indigo-600 border-b-2 border-indigo-600').addClass('text-gray-500');
		$(this).addClass('text-indigo-600 border-b-2 border-indigo-600').removeClass('text-gray-500');

		$('.fbmp-tab-panel').addClass('hidden');
		$('#fbmp-tab-' + tab).removeClass('hidden');
	});

	// Upgrade to Premium -> Stripe Checkout
	$('#fbmp-upgrade-btn').on('click', function () {
		const $btn = $(this);
		const $msg = $('#fbmp-upgrade-message');
		$btn.prop('disabled', true).text('Redirecting...');

		postAjax('fbmp_create_checkout', {}, $msg, function (result) {
			window.location.href = result.checkout_url;
		});

		// Re-enable if something went wrong and we're still here after a beat.
		setTimeout(function () {
			$btn.prop('disabled', false).text('Upgrade to Premium');
		}, 4000);
	});

	// Copy referral link
	$('#fbmp-copy-ref').on('click', function () {
		const $input = $('#fbmp-ref-link');
		$input.select();
		document.execCommand('copy');
		const $btn = $(this);
		const original = $btn.text();
		$btn.text('Copied!');
		setTimeout(function () {
			$btn.text(original);
		}, 1500);
	});

	// Admin Rescue: toggle the box
	$('#fbmp-rescue-toggle').on('click', function (e) {
		e.preventDefault();
		$('#fbmp-rescue-box').toggleClass('hidden');
	});

	// Admin Rescue: request the email
	$('#fbmp-rescue-form').on('submit', function (e) {
		e.preventDefault();
		const $msg = $('#fbmp-rescue-message');
		const data = { email: $(this).find('[name=email]').val() };
		const $form = $(this);
		postAjax('fbmp_request_rescue', data, $msg, function (result) {
			showMessage($msg, result.message, false);
			$form[0].reset();
		});
	});
});
