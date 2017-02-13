(function ($) {

    Shopsys = window.Shopsys || {};
    Shopsys.productImagesDelete = Shopsys.productImagesDelete || {};

    Shopsys.productImagesDelete.init = function () {

        $('.js-product-images-image').each(function () {
            var $image = $(this);
            var $imagePreview = $image.find('.js-product-images-preview');
            var $deleteButton = $image.find('.js-product-images-delete-button');
            var $revertButton = $image.find('.js-product-images-delete-revert-button');
            var $deleteInfo = $image.find('.js-product-images-image-overlay');
            var imageId = $image.data('id');

            $deleteButton.bind('click.deleteImage', function() {
                Shopsys.choiceControl.select('#product_edit_form_imagesToDelete', imageId);
                $imagePreview.addClass('list-images__item__in--removed');
                $deleteButton.hide();
                $revertButton.show();
                $deleteInfo.show();
                Shopsys.formChangeInfo.showInfo();
                return false;
            });

            $revertButton.bind('click.deleteImage', function() {
                Shopsys.choiceControl.deselect('#product_edit_form_imagesToDelete', imageId);
                $imagePreview.removeClass('list-images__item__in--removed');
                $deleteButton.show();
                $revertButton.hide();
                $deleteInfo.hide();
                return false;
            });

            var imageIds = Shopsys.choiceControl.getSelectedValues('#product_edit_form_imagesToDelete');
            if ($.inArray(imageId, imageIds) !== -1) {
                $deleteButton.trigger('click.deleteImage');
            }
        });
    };

    $(document).ready(function () {
        Shopsys.productImagesDelete.init();
    });

})(jQuery);
