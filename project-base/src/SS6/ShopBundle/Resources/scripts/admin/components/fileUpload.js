(function ($) {

	SS6 = window.SS6 || {};
	SS6.fileUpload = SS6.fileUpload || {};

	var fileUpload = function ($container) {
		$container.find('.js-file-upload').each(function() {
			var uploader = new SS6.fileUpload.Uploader($(this));
			uploader.init();
		});
	};

	SS6.register.registerCallback(fileUpload);

	SS6.fileUpload.Uploader = function($uploader) {
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

		self.init = function() {
			$uploader.closest('form').submit(onFormSubmit);
			initUploadedFiles();
			initUploader();
		};

		var initUploadedFiles = function() {
			$uploadedFiles.find('.js-file-upload-uploaded-file').each(function () {
				var fileItem = new SS6.fileUpload.FileItem(self, $(this), true);
				fileItem.init();
			});
		};

		var initUploader = function() {
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

		this.deleteTemporaryFile = function(filename) {
			$.ajax({
				url: deleteUrl,
				type: 'POST',
				data: {filename: filename},
				dataType: 'json'
			});
		};

		var createNewUploadedFile = function() {
			var templateHtml = $uploadedFiles.data('prototype').replace(/__name__/g, '');
			var $uploadedFileTemplate = $($.parseHTML(templateHtml));
			$uploadedFileTemplate.find('*[id]').removeAttr('id');

			return $uploadedFileTemplate;
		};

		var updateFileStatus = function(status, message) {
			$status.parent().stop(true, true).show();
			$status.text(message).removeClass('error success uploading').addClass(status);
		};

		var onFormSubmit = function (event) {
			if (!ready) {
				SS6.window({
					content: SS6.translator.trans('Prosím počkejte dokud nebudou nahrány všechny soubory a zkuste to znovu.')
				});
				event.preventDefault();
			}
		};

		var onBeforeUpload = function() {
			ready = false;
			updateFileStatus('uploading', SS6.translator.trans('Nahrávám...'));
		};

		var onUploadNewFile = function(id, file) {
			var $uploadedfile = createNewUploadedFile();
			$uploadedfile.show();
			items[id] = new SS6.fileUpload.FileItem(self, $uploadedfile);
			items[id].init();
			items[id].setLabel(file.name, file.size);
			$uploadedFiles.append($uploadedfile);
		};

		var onUploadComplete = function() {
			ready = true;
			SS6.validation.forceValidateElement($uploader);
		};

		var onUploadProgress = function(id, percent) {
			items[id].setProgress(percent);
			updateFileStatus('uploading', SS6.translator.trans('Nahrávám...'));
		};

		var onUploadSuccess = function(id, data) {
			if (data.status === 'success') {
				if (lastUploadItemId !== null && multiple === false) {
					items[lastUploadItemId].deleteItem();
				}
				lastUploadItemId = id;
				items[id].setAsUploaded(data.filename, data.iconType, data.imageThumbnailUri);
				updateFileStatus('success', SS6.translator.trans('Úspěšně nahráno'));
				$status.parent().fadeOut(4000);
				SS6.formChangeInfo.showInfo();
			} else {
				items[id].deleteItem();
				SS6.window({
					content: SS6.translator.trans('Při nahrávání souboru došlo k chybě.')
				});
			}
		};

		var onUploadError = function(id, message, code) {
			items[id].deleteItem();
			if (code === 413) {
				message = SS6.translator.trans('Soubor je příliš velký');
			} else if (code === 415) {
				message = SS6.translator.trans('Soubor je v nepodporovaném formátu');
			}
			SS6.window({
				content: SS6.translator.trans('Při nahrávání souboru došlo k chybě: %message%', {'%message%': message })
			});
		};

		var onFallbackMode = function() {
			$fallbackHide.hide();
		};
	};

})(jQuery);
