(function ($) {

	SS6 = window.SS6 || {};
	SS6.productImagesDelete = SS6.productImagesDelete || {};

	SS6.productImagesDelete.init = function () {

		$('.js-product-images-image').each(function () {
			var $image = $(this);
			var $imagePreview = $image.find('.js-product-images-preview');
			var $deleteButton = $image.find('.js-product-images-delete-button');
			var $revertButton = $image.find('.js-product-images-delete-revert-button');
			var $deleteInfo = $image.find('.js-product-images-image-overlay');
			var imageId = $image.data('id');

			$deleteButton.bind('click.deleteImage', function() {
				SS6.choiceControl.select('#product_edit_form_imagesToDelete', imageId);
				$imagePreview.addClass('list-images__item__in--removed');
				$deleteButton.hide();
				$revertButton.show();
				$deleteInfo.show();
				SS6.formChangeInfo.showInfo();
				return false;
			});

			$revertButton.bind('click.deleteImage', function() {
				SS6.choiceControl.deselect('#product_edit_form_imagesToDelete', imageId);
				$imagePreview.removeClass('list-images__item__in--removed');
				$deleteButton.show();
				$revertButton.hide();
				$deleteInfo.hide();
				return false;
			});

			var imageIds = SS6.choiceControl.getSelectedValues('#product_edit_form_imagesToDelete');
			if ($.inArray(imageId, imageIds) !== -1) {
				$deleteButton.trigger('click.deleteImage');
			}
		});
	};

	$(document).ready(function () {
		SS6.productImagesDelete.init();
	});

})(jQuery);
