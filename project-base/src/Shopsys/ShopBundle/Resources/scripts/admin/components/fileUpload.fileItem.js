(function ($) {

    Shopsys = window.Shopsys || {};
    Shopsys.fileUpload = Shopsys.fileUpload || {};

    Shopsys.fileUpload.FileItem = function (uploader, $file, loaded) {
        var self = this;
        var $label = $file.find('.js-file-upload-label');
        var $deleteButton = $file.find('.js-file-upload-delete');
        var $progress = $file.find('.js-file-upload-progress');
        var $progressBar = $file.find('.js-file-upload-progress-bar');
        var $progressBarValue = $file.find('.js-file-upload-progress-bar-value');
        var $input = $file.find('.js-file-upload-input');
        var $iconType = $file.find('.js-file-upload-icon-type');
        var $imageThumbnail = $file.find('.js-file-upload-image-thumbnail');

        this.init = function () {
            $progress.hide();
            $deleteButton.click(self.deleteItem);
            if (loaded !== true) {
                $iconType.hide();
                $imageThumbnail.hide();
            }
        };

        this.setAsUploaded = function (filename, iconType, imageThumbnailUri) {
            $input.val(filename);
            setIconType(iconType);
            setImageThumbnail(imageThumbnailUri);
        };

        this.deleteItem = function () {
            uploader.deleteTemporaryFile($input.val());
            $file.remove();
            Shopsys.validation.forceValidateElement(uploader.$uploader);
        };

        this.setLabel = function (filename, filesize) {
            var sizeInMB = Math.round(filesize / 1000 / 1000 * 100) / 100; //https://en.wikipedia.org/wiki/Binary_prefix
            $label.text(filename + ' (' + sizeInMB + ' MB)');
        };

        this.setProgress = function (percent) {
            $progress.show();
            $progressBar.width(percent + '%');
            $progressBarValue.text(percent + '%');

            if (percent === 100) {
                setTimeout(function () {
                    $progress.fadeOut();
                }, 1000);
            }
        };

        var setImageThumbnail = function (imageThumbnailUri) {
            if (imageThumbnailUri !== null) {
                $imageThumbnail.attr('src', imageThumbnailUri).show();
            }
        };

        var setIconType = function (iconType) {
            if (iconType !== null) {
                $iconType
                    .attr('class', $iconType.attr('class').replace(/__icon-type__/g, iconType))
                    .show();
            }
        };
    };

})(jQuery);
