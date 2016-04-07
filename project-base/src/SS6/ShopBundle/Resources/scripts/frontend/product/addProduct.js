(function ($) {

	SS6 = window.SS6 || {};
	SS6.addProduct = SS6.addProduct || {};

	SS6.addProduct.init = function ($container) {
		$container.filterAllNodes('form.js-add-product').bind('submit.addProductAjaxSubmit', SS6.addProduct.ajaxSubmit);
	};

	SS6.addProduct.ajaxSubmit = function (event) {
		SS6.ajax({
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

	SS6.addProduct.onError = function (jqXHR) {
		// on FireFox abort ajax request, but request was probably successful
		if (jqXHR.status !== 0) {
			SS6.window({
				content: SS6.translator.trans('Operace se nezdařila')
			});
		}
	};

	SS6.register.registerCallback(SS6.addProduct.init);

})(jQuery);
