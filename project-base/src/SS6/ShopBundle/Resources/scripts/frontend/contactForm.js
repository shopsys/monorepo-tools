(function ($) {

	SS6 = window.SS6 || {};
	SS6.contactForm = SS6.contactForm || {};

	SS6.register.registerCallback(function () {
		$('form[name="contact_form"]').bind('contactFormAjaxSumbit', SS6.contactForm.ajaxSumbit);
	});

	SS6.contactForm.ajaxSumbit = function (event) {
		$('#js-contact-form-spinner').show();
		$(this).addClass('js-disable');
		$.ajax({
			url: $(this).attr('action'),
			method: 'post',
			data: $(this).serialize(),
			dataType: 'json',
			success: onSuccess
		});
		event.preventDefault();
	};

	var onSuccess = function (data) {
		$('#js-contact-form-spinner').hide();
		$('#js-contact-form-container').html(data['contactFormHtml']);
		SS6.register.registerNewContent($('#js-contact-form-container'));
		var message = data['errorMessages'].splice(0) + data['successMessages'].splice(0);
		SS6.window({
			content: message
		});
	};

})(jQuery);
