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
			loaderElement: subscriptionFormSelector,
			url: $(this).attr('action'),
			method: 'post',
			data: $(this).serialize(),
			success: onSuccess
		});
	};

	var onSuccess = function (data) {
		$(subscriptionFormSelector).replaceWith(data);

		// must select again from modified DOM, because replaceWith() do not change previous jQuery collection
		var $newContent = $(subscriptionFormSelector);
		SS6.register.registerNewContent($newContent);
	};

})(jQuery);
