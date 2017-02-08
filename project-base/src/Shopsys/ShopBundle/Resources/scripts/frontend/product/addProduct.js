(function ($) {

	Shopsys = window.Shopsys || {};
	Shopsys.addProduct = Shopsys.addProduct || {};

	Shopsys.addProduct.init = function ($container) {
		$container.filterAllNodes('form.js-add-product').bind('submit.addProductAjaxSubmit', Shopsys.addProduct.ajaxSubmit);
	};

	Shopsys.addProduct.ajaxSubmit = function (event) {
		Shopsys.ajax({
			url: $(this).data('ajax-url'),
			type: 'POST',
			data: $(this).serialize(),
			dataType: 'html',
			success: Shopsys.addProduct.onSuccess,
			error: Shopsys.addProduct.onError
		});

		event.preventDefault();
	};

	Shopsys.addProduct.onSuccess = function (data) {
		var buttonContinueUrl = $($.parseHTML(data)).filterAllNodes('.js-add-product-url-cart').data('url');
		var isWide = $($.parseHTML(data)).filterAllNodes('.js-add-product-wide-window').data('wide');
		if (isWide) {
			var cssClass = 'window-popup--wide';
		} else {
			var cssClass = 'window-popup--standard';
		}

		if (buttonContinueUrl !== undefined) {
			Shopsys.window({
				content: data,
				cssClass: cssClass,
				buttonContinue: true,
				textContinue: Shopsys.translator.trans('Go to cart'),
				urlContinue: buttonContinueUrl,
				cssClassContinue: 'btn--success'
			});

			$('#js-cart-box').trigger('reload');
		} else {
			Shopsys.window({
				content: data,
				cssClass: cssClass,
				buttonCancel: true,
				textCancel: Shopsys.translator.trans('Close'),
				cssClassCancel: 'btn--success'
			});
		}
	};

	Shopsys.addProduct.onError = function (jqXHR) {
		// on FireFox abort ajax request, but request was probably successful
		if (jqXHR.status !== 0) {
			Shopsys.window({
				content: Shopsys.translator.trans('Operation failed')
			});
		}
	};

	Shopsys.register.registerCallback(Shopsys.addProduct.init);

})(jQuery);
