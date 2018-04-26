(function ($) {

    Shopsys = window.Shopsys || {};
    Shopsys.productImagesDelete = Shopsys.productImagesDelete || {};

    Shopsys.productImagesDelete.init = function () {

        $('.js-image-upload-image').each(function () {
            var $image = $(this);
            var $imagePreview = $image.find('.js-image-upload-preview');
            var $deleteButton = $image.find('.js-image-upload-delete-button');
            var $revertButton = $image.find('.js-image-upload-delete-revert-button');
            var $deleteInfo = $image.find('.js-image-upload-image-overlay');
            var imageId = $image.data('id');

            $deleteButton.bind('click.deleteImage', function () {
                Shopsys.choiceControl.select($image.data('delete-input'), imageId);
                $imagePreview.addClass('list-images__item__in--removed');
                $deleteButton.hide();
                $revertButton.show();
                $deleteInfo.show();
                Shopsys.formChangeInfo.showInfo();
                return false;
            });

            $revertButton.bind('click.deleteImage', function () {
                Shopsys.choiceControl.deselect($image.data('delete-input'), imageId);
                $imagePreview.removeClass('list-images__item__in--removed');
                $deleteButton.show();
                $revertButton.hide();
                $deleteInfo.hide();
                return false;
            });

            var imageIds = Shopsys.choiceControl.getSelectedValues($image.data('delete-input'));
            if ($.inArray(imageId, imageIds) !== -1) {
                $deleteButton.trigger('click.deleteImage');
            }
        });
    };

    $(document).ready(function () {
        Shopsys.productImagesDelete.init();
    });

})(jQuery);
