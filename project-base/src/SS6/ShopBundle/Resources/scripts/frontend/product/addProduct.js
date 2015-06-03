(function ($) {

	SS6 = window.SS6 || {};
	SS6.addProduct = SS6.addProduct || {};

	SS6.addProduct.init = function ($container) {
		$container.find('form.js-add-product').bind('submit.addProductAjaxSubmit', SS6.addProduct.ajaxSubmit);
	};

	SS6.addProduct.ajaxSubmit = function (event) {
		$.ajax({
			url: $(this).data('ajax-url'),
			type: 'POST',
			data: $(this).serialize(),
			dataType: 'html',
			success: SS6.addProduct.onSuccess,
			error: SS6.addProduct.onError
		});

		event.preventDefault();
	};

	SS6.addProduct.onSuccess = function (data) {
		SS6.window({
			content: data,
			wide: true
		});

		$('#cart-box').trigger('reload');
	};

	SS6.addProduct.onError = function () {
		SS6.window({
			content: SS6.translator.trans('Operace se nezdařila')
		});
	};

	SS6.register.registerCallback(SS6.addProduct.init);

})(jQuery);
