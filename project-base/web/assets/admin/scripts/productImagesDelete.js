(function ($) {

	SS6 = window.SS6 || {};
	SS6.productImagesDelete = SS6.productImagesDelete || {};

	SS6.productImagesDelete.init = function () {
		SS6.productImagesDelete.updateCss();
	};

	SS6.productImagesDelete.deleteImage = function (imageId) {
		SS6.choiceControl.select('#product_imagesToDelete', imageId);
		SS6.productImagesDelete.updateCss();
	};

	SS6.productImagesDelete.revert = function () {
		SS6.choiceControl.deselectAll('#product_imagesToDelete');
		SS6.productImagesDelete.updateCss();
	};

	SS6.productImagesDelete.updateCss = function () {
		var imageIds = SS6.choiceControl.getSelectedValues('#product_imagesToDelete');
		$('.js-image-to-delete').each(function (key, element) {
			var $element = $(element);
			if ($.inArray($element.data('id'), imageIds) !== -1) {
				$element.addClass('list-image__item__in--removed');
			} else {
				$element.removeClass('list-image__item__in--removed');
			}
		});
	};

	$(document).ready(function () {
		SS6.productImagesDelete.init();
	});

})(jQuery);
