(function ($) {

	SS6 = window.SS6 || {};
	SS6.contactForm = SS6.contactForm || {};

	SS6.register.registerCallback(function ($container) {
		$container.filterAllNodes('form[name="contact_form"]').bind('contactFormAjaxSumbit', SS6.contactForm.ajaxSumbit);
	});

	SS6.contactForm.ajaxSumbit = function (event) {
		SS6.ajax({
			loaderElement: '#js-contact-form-container',
			url: $(this).attr('action'),
			method: 'post',
			data: $(this).serialize(),
			dataType: 'json',
			success: onSuccess
		});
		event.preventDefault();
	};

	var onSuccess = function (data) {
		$('#js-contact-form-container').html(data['contactFormHtml']);
		SS6.register.registerNewContent($('#js-contact-form-container'));
		SS6.window({
			content: data['message']
		});
	};

})(jQuery);
