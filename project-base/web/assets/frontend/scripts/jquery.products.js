(function ($) {

	$.fn.SS6 = $.fn.SS6 || {};
	$.fn.SS6.productDetail = $.fn.SS6.productDetail || {};
	
	$.fn.SS6.productDetail.init = function () {
		$('.js-gallery').magnificPopup({
			type: 'image'
		});
	};
	
	$(document).ready(function () {
		$.fn.SS6.productDetail.init();
	});
	
})(jQuery);
