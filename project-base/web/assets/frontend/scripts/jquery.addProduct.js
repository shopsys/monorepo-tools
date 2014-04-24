/**
 * Custom plugin window
 */

(function ($) {

	$.fn.SS6 = $.fn.SS6 || {};
	$.fn.SS6.addProduct = $.fn.SS6.addProduct || {};
	
	$.fn.SS6.addProduct.init = function (formElement) {
		$('form.add-product').bind('submit.addProductAjaxSubmit', $.fn.SS6.addProduct.ajaxSubmit);
	}
	
	$.fn.SS6.addProduct.ajaxSubmit = function (event) {
		$.ajax({
			url: $(this).attr('action'),
			type: 'POST',
			data: $(this).serialize(),
			dataType: 'json',
			success: $.fn.SS6.addProduct.processResponse
		});
		
		event.preventDefault();
	}
	
	$.fn.SS6.addProduct.processResponse = function (data) {
		var jsWindowId = $(data.jsWindow).filter('.window-container:first').attr('id');
		$('#' + jsWindowId).remove();
		$('body').append(data.jsWindow);
		$.fn.SS6.window.open(data.jsWindowId);
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
		$.fn.SS6.addProduct.init();
	});
	
})(jQuery);


