(function ($) {

	SS6 = window.SS6 || {};
	SS6.addProduct = SS6.addProduct || {};
	
	SS6.addProduct.init = function (formElement) {
		$('form.add-product').bind('submit.addProductAjaxSubmit', SS6.addProduct.ajaxSubmit);
	}
	
	SS6.addProduct.ajaxSubmit = function (event) {
		$.ajax({
			url: $(this).attr('action'),
			type: 'POST',
			data: $(this).serialize(),
			dataType: 'json',
			success: SS6.addProduct.processResponse
		});
		
		event.preventDefault();
	}
	
	SS6.addProduct.processResponse = function (data) {
		var jsWindowId = $(data.jsWindow).filter('.window:first').attr('id');
		$('#' + jsWindowId).remove();
		$('body').append(data.jsWindow);
		SS6.window.open(data.jsWindowId);
		if (data.cartBoxReloadUrl) {
			$.ajax({
				url: data.cartBoxReloadUrl,
				type: 'get',
				success: function (data) {
					$('#cart-box').replaceWith(data);
				}
			});
		}
	}
	
	$(document).ready(function () {
		SS6.addProduct.init();
	});
	
})(jQuery);
