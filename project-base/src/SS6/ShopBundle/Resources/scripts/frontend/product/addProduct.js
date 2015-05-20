(function ($) {

	SS6 = window.SS6 || {};
	SS6.addProduct = SS6.addProduct || {};

	SS6.addProduct.init = function () {
		$('form.js-add-product').bind('submit.addProductAjaxSubmit', SS6.addProduct.ajaxSubmit);
	};

	SS6.addProduct.ajaxSubmit = function (event) {
		$.ajax({
			url: $(this).attr('action'),
			type: 'POST',
			data: $(this).serialize(),
			dataType: 'json',
			success: SS6.addProduct.processResponse
		});

		event.preventDefault();
	};

	SS6.addProduct.processResponse = function (data) {
		var options = {
			content: data.message,
			buttonContinue: data.success,
			textContinue: SS6.translator.trans('Přejít do košíku'),
			urlContinue: data.continueUrl
		};
		SS6.window(options);
		if (data.success && data.cartBoxReloadUrl) {
			$.ajax({
				url: data.cartBoxReloadUrl,
				type: 'get',
				success: function (data) {
					$('#cart-box').replaceWith(data);
				}
			});
		}
	};

	$(document).ready(function () {
		SS6.addProduct.init();
	});

})(jQuery);
