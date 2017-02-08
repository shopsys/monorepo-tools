(function ($) {

	SS6 = window.SS6 || {};
	SS6.productImagesSort = SS6.productImagesSort || {};

	SS6.productImagesSort.init = function () {
		$('#js-product-images').sortable({
			handle: '.js-product-images-image-handle',
			update: SS6.formChangeInfo.showInfo
		});
	};

	$(document).ready(function () {
		SS6.productImagesSort.init();
	});

})(jQuery);
