(function ($) {

	SS6 = window.SS6 || {};
	SS6.productImagesSort = SS6.productImagesSort || {};

	SS6.productImagesSort.init = function () {
		$('#js-product-images').sortable({
			handle: '.js-product-images-image-handle',
			update: SS6.productImagesSort.highlightMainImage
		});
		SS6.productImagesSort.highlightMainImage();
	};

	SS6.productImagesSort.highlightMainImage = function () {
		$('#js-product-images .list-image__item--main').removeClass('list-image__item--main');
		$('#js-product-images .js-product-images-image:first').addClass('list-image__item--main');
	};

	$(document).ready(function () {
		SS6.productImagesSort.init();
	});

})(jQuery);
