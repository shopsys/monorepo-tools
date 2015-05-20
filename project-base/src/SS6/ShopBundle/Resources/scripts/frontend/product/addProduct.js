(function ($) {

	SS6 = window.SS6 || {};
	SS6.addProduct = SS6.addProduct || {};

	SS6.addProduct.init = function () {
		$('form.js-add-product').bind('submit.addProductAjaxSubmit', SS6.addProduct.ajaxSubmit);
	};

	SS6.addProduct.ajaxSubmit = function (event) {
		$.ajax({
			url: $(this).data('ajax-url'),
			type: 'POST',
			data: $(this).serialize(),
			dataType: 'html',
			success: SS6.addProduct.processResponse
		});

		event.preventDefault();
	};

	SS6.addProduct.processResponse = function (data) {
		SS6.window({
			content: data
		});

		$('#cart-box').trigger('reload');
	};

	$(document).ready(function () {
		SS6.addProduct.init();
	});

})(jQuery);
