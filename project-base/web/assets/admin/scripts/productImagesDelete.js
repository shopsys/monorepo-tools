(function ($) {

	SS6 = window.SS6 || {};
	SS6.productImagesDelete = SS6.productImagesDelete || {};

	SS6.productImagesDelete.init = function () {
		SS6.productImagesDelete.updateCss();
	};

	SS6.productImagesDelete.deleteImage = function (imageId) {
		SS6.choiceControl.select('#product_edit_imagesToDelete', imageId);
		SS6.productImagesDelete.updateCss();
	};

	SS6.productImagesDelete.revert = function (imageId) {
		SS6.choiceControl.deselect('#product_edit_imagesToDelete', imageId);
		SS6.productImagesDelete.updateCss();
	};

	SS6.productImagesDelete.updateCss = function () {
		var imageIds = SS6.choiceControl.getSelectedValues('#product_edit_imagesToDelete');
		$('.js-image-to-delete').each(function (key, element) {
			var $element = $(element);
			if ($.inArray($element.data('id'), imageIds) !== -1) {
				$element.addClass('list-image__item__in--removed');
				$(this).closest('.js-image-delete-button-parent').find('.js-image-delete-button').addClass('display-none');
				$(this).closest('.js-image-delete-button-parent').find('.js-image-delete-confirmed').removeClass('display-none');
			} else {
				$element.removeClass('list-image__item__in--removed');
				$(this).closest('.js-image-delete-button-parent').find('.js-image-delete-confirmed').addClass('display-none');
				$(this).closest('.js-image-delete-button-parent').find('.js-image-delete-button').removeClass('display-none');
			}
		});
	};

	$(document).ready(function () {
		SS6.productImagesDelete.init();
	});

})(jQuery);
