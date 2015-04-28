(function ($) {

	SS6 = window.SS6 || {};
	SS6.productImagesDelete = SS6.productImagesDelete || {};

	SS6.productImagesDelete.init = function () {

		$('.js-product-images-image').each(function () {
			var $image = $(this);
			var $imagePreview = $image.find('.js-product-images-preview');
			var $delete = $image.find('.js-product-images-delete-button');
			var $deleteButton = $delete.find('button');
			var $revert = $image.find('.js-product-images-delete-revert-button');
			var $revertButton = $revert.find('button');
			var imageId = $image.data('id');

			$deleteButton.bind('click.deleteImage', function() {
				SS6.choiceControl.select('#product_edit_imagesToDelete', imageId);
				$imagePreview.addClass('list-image__item__in--removed');
				$delete.hide();
				$revert.show();
				SS6.formChangeInfo.showInfo();
				return false;
			});

			$revertButton.bind('click.deleteImage', function() {
				SS6.choiceControl.deselect('#product_edit_imagesToDelete', imageId);
				$imagePreview.removeClass('list-image__item__in--removed');
				$delete.show();
				$revert.hide();
				return false;
			});

			var imageIds = SS6.choiceControl.getSelectedValues('#product_edit_imagesToDelete');
			if ($.inArray(imageId, imageIds) !== -1) {
				$deleteButton.trigger('click.deleteImage');
			}
		});
	};

	$(document).ready(function () {
		SS6.productImagesDelete.init();
	});

})(jQuery);
