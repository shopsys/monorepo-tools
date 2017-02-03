(function ($) {

	SS6 = window.SS6 || {};
	SS6.newsletterSubscriptionForm = SS6.newsletterSubscriptionForm || {};

	var subscriptionFormSelector = 'form[name="newsletter_subscription_form"]';

	SS6.register.registerCallback(function ($container) {
		$container.filterAllNodes(subscriptionFormSelector)
			.bind('ajaxSumbit', SS6.newsletterSubscriptionForm.ajaxSumbit);
	});

	SS6.newsletterSubscriptionForm.ajaxSumbit = function () {
		SS6.ajax({
			loaderElement: 'body',
			url: $(this).attr('action'),
			method: 'post',
			data: $(this).serialize(),
			success: onSuccess
		});
	};

	var onSuccess = function (data) {
		$(subscriptionFormSelector).replaceWith(data);

		// We must select again from modified DOM, because replaceWith() does not change previous jQuery collection.
		var $newContent = $(subscriptionFormSelector);
		var $emailInput = $newContent.find('input[name="newsletter_subscription_form[email]"]');

		SS6.register.registerNewContent($newContent);
		if ($newContent.data('success')) {
			$emailInput.val('');

			SS6.window({
				content: SS6.translator.trans('Byli jste úspěšně přihlášeni k odběru našeho newsletteru.'),
				buttonCancel: true,
				textCancel: SS6.translator.trans('Zavřít')
			});
		}
	};

})(jQuery);
