(function ($) {

	SS6 = window.SS6 || {};
	SS6.productDetail = SS6.productDetail || {};
	
	SS6.productDetail.init = function () {
		$('.js-gallery').magnificPopup({
			type: 'image'
		});
	};
	
	$(document).ready(function () {
		SS6.productDetail.init();
	});
	
})(jQuery);
