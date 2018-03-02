(function ($) {

    Shopsys = window.Shopsys || {};
    Shopsys.fileUpload = Shopsys.fileUpload || {};

    var fileUpload = function ($container) {
        $container.filterAllNodes('.js-file-upload').each(function () {
            var uploader = new Shopsys.fileUpload.Uploader($(this));
            uploader.init();
        });
    };

    Shopsys.register.registerCallback(fileUpload);

    Shopsys.fileUpload.Uploader = function ($uploader) {
        var self = this;
        var $uploadedFiles = $uploader.find('.js-file-upload-uploaded-files');
        var $status = $uploader.find('.js-file-upload-status');
        var $fallbackHide = $uploader.find('.js-file-upload-fallback-hide');
        var multiple = $uploader.find('input[type=file]').attr('multiple') === 'multiple';
        var deleteUrl = $uploader.data('fileupload-delete-url');
        var ready = true;
        var items = [];
        var lastUploadItemId = null;
        this.$uploader = $uploader;

        self.init = function () {
            $uploader.closest('form').submit(onFormSubmit);
            initUploadedFiles();
            initUploader();
        };

        var initUploadedFiles = function () {
            $uploadedFiles.find('.js-file-upload-uploaded-file').each(function () {
                var fileItem = new Shopsys.fileUpload.FileItem(self, $(this), true);
                fileItem.init();
            });
        };

        var initUploader = function () {
            $uploader.dmUploader({
                url: $uploader.data('fileupload-url'),
                dataType: 'json',
                onBeforeUpload: onBeforeUpload,
                onNewFile: onUploadNewFile,
                onComplete: onUploadComplete,
                onUploadProgress: onUploadProgress,
                onUploadSuccess: onUploadSuccess,
                onUploadError: onUploadError,
                onFallbackMode: onFallbackMode
            });
        };

        this.deleteTemporaryFile = function (filename) {
            Shopsys.ajax({
                url: deleteUrl,
                type: 'POST',
                data: {filename: filename},
                dataType: 'json'
            });
        };

        var createNewUploadedFile = function () {
            var templateHtml = $uploadedFiles.data('prototype').replace(/__name__/g, '');
            var $uploadedFileTemplate = $($.parseHTML(templateHtml));
            $uploadedFileTemplate.find('*[id]').removeAttr('id');

            return $uploadedFileTemplate;
        };

        var updateFileStatus = function (status, message) {
            $status.parent().stop(true, true).show();
            $status.text(message).removeClass('error success uploading').addClass(status);
        };

        var onFormSubmit = function (event) {
            if (!ready) {
                Shopsys.window({
                    content: Shopsys.translator.trans('Please wait until all files are uploaded and try again.')
                });
                event.preventDefault();
            }
        };

        var onBeforeUpload = function () {
            ready = false;
            updateFileStatus('uploading', Shopsys.translator.trans('Uploading...'));
        };

        var onUploadNewFile = function (id, file) {
            var $uploadedfile = createNewUploadedFile();
            $uploadedfile.show();
            items[id] = new Shopsys.fileUpload.FileItem(self, $uploadedfile);
            items[id].init();
            items[id].setLabel(file.name, file.size);
            $uploadedFiles.append($uploadedfile);
        };

        var onUploadComplete = function () {
            ready = true;
            Shopsys.validation.forceValidateElement($uploader);
        };

        var onUploadProgress = function (id, percent) {
            items[id].setProgress(percent);
            updateFileStatus('uploading', Shopsys.translator.trans('Uploading...'));
        };

        var onUploadSuccess = function (id, data) {
            if (data.status === 'success') {
                if (lastUploadItemId !== null && multiple === false) {
                    items[lastUploadItemId].deleteItem();
                }
                lastUploadItemId = id;
                items[id].setAsUploaded(data.filename, data.iconType, data.imageThumbnailUri);
                updateFileStatus('success', Shopsys.translator.trans('Successfully uploaded'));
                $status.parent().fadeOut(4000);
                Shopsys.formChangeInfo.showInfo();
            } else {
                items[id].deleteItem();
                Shopsys.window({
                    content: Shopsys.translator.trans('Error occurred while uploading file.')
                });
            }
        };

        var onUploadError = function (id, message, code) {
            items[id].deleteItem();
            if (code === 413) {
                message = Shopsys.translator.trans('File is too big');
            } else if (code === 415) {
                message = Shopsys.translator.trans('File is in unsupported format');
            }
            Shopsys.window({
                content: Shopsys.translator.trans('Error occurred while uploading file: %message%', { '%message%': message })
            });
        };

        var onFallbackMode = function () {
            $fallbackHide.hide();
        };
    };

})(jQuery);
