(function ($) {

	SS6 = window.SS6 || {};
	SS6.productDetail = SS6.productDetail || {};

	SS6.productDetail.init = function () {
		$('.js-gallery-main').magnificPopup({
			type: 'image'
		});

		$('.js-gallery').magnificPopup({
			type: 'image',
			delegate: 'a',
			gallery: {
				enabled: true,
				navigateByImgClick: true,
				preload: [0,1]
			}
		});
	};

	$(document).ready(function () {
		SS6.productDetail.init();
	});

})(jQuery);
