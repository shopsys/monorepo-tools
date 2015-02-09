(function ($) {

	SS6 = window.SS6 || {};
	SS6.fileUpload = SS6.fileUpload || {};
	SS6.fileUpload.fileItem = SS6.fileUpload.fileItem || {};

	SS6.fileUpload.fileItem.constructor = function (uploader, $file, loaded) {
		var self = this;
		var $label = $file.find('.js-file-upload-label');
		var $deleteButton = $file.find('.js-file-upload-delete');
		var $progress = $file.find('.js-file-upload-progress');
		var $progressBar = $file.find('.js-file-upload-progress-bar');
		var $input = $file.find('.js-file-upload-input');
		var $iconType = $file.find('.js-file-upload-icon-type');
		var $imageThumbnail = $file.find('.js-file-upload-image-thumbnail');

		self.init = function () {
			$progress.hide();
			$deleteButton.click(self.deleteItem);
			if (loaded !== true) {
				$iconType.hide();
				$imageThumbnail.hide();
			}
		};

		self.setAsUploaded = function (filename, iconType, imageThumbnailUri) {
			$input.val(filename);
			setIconType(iconType);
			setImageThumbnail(imageThumbnailUri);
		};

		self.deleteItem = function () {
			uploader.deleteTemporaryFile($input.val());
			$file.remove();
		};

		self.setLabel = function (filename, filesize) {
			var sizeInMB = Math.round(filesize / 1024 / 1024 * 100) / 100;
			$label.text(filename + ' (' + sizeInMB + ' MB)');
		};

		self.setProgress = function (percent) {
			$progress.show();
			$progressBar.width(percent + '%').text(percent + '%');

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
					.removeClass('fa-__icon-type__')
					.addClass('fa-' + iconType)
					.show();
			}
		};
	}

})(jQuery);