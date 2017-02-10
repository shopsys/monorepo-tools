(function ($) {

	Shopsys = window.Shopsys || {};
	Shopsys.newsletterSubscriptionForm = Shopsys.newsletterSubscriptionForm || {};

	var subscriptionFormSelector = 'form[name="newsletter_subscription_form"]';

	Shopsys.register.registerCallback(function ($container) {
		$container.filterAllNodes(subscriptionFormSelector)
			.bind('ajaxSumbit', Shopsys.newsletterSubscriptionForm.ajaxSumbit);
	});

	Shopsys.newsletterSubscriptionForm.ajaxSumbit = function () {
		Shopsys.ajax({
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

		Shopsys.register.registerNewContent($newContent);
		if ($newContent.data('success')) {
			$emailInput.val('');

			Shopsys.window({
				content: Shopsys.translator.trans('You have been successfully subscribed to our newsletter.'),
				buttonCancel: true,
				textCancel: Shopsys.translator.trans('Close')
			});
		}
	};

})(jQuery);
