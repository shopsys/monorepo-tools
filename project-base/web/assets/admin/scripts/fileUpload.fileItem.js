(function ($) {

	SS6 = window.SS6 || {};
	SS6.fileUpload = SS6.fileUpload || {};

	SS6.fileUpload.fileItem = function (uploader, $file, loaded) {
		var $label = $file.find('.js-file-upload-label');
		var $delete = $file.find('.js-file-upload-delete');
		var $progress = $file.find('.js-file-upload-progress');
		var $progressBar = $file.find('.js-file-upload-progress-bar');
		var $input = $file.find('.js-file-upload-input');
		var $iconType = $file.find('.js-file-upload-icon-type');
		var $imageThumbnail = $file.find('.js-file-upload-image-thumbnail');

		$progress.hide();

		if (loaded !== true) {
			$iconType.hide();
			$imageThumbnail.hide();
		}

		this.setAsUploaded = function (filename, iconType, imageThumbnailUri) {
			$input.val(filename);
			setIconType(iconType);
			setImageThumbnail(imageThumbnailUri);
		}

		this.deleteItem = function () {
			uploader.tryDeleteTemporaryFile($input.val());
			$file.remove();
		}
		$delete.bind('click', this.deleteItem);

		this.setLabel = function (filename, filesize) {
			var sizeInMB = Math.round(filesize / 1024 / 1024 * 100) / 100;
			$label.text(filename + ' (' + sizeInMB + ' MB)');
		}

		this.setProgress = function (percent) {
			$progress.show();
			$progressBar.width(percent + '%').text(percent + '%');

			if (percent === 100) {
				setTimeout(function () {
					$progress.fadeOut();
				}, 1000);
			}
		}

		var setImageThumbnail = function (imageThumbnailUri) {
			if (imageThumbnailUri !== null) {
				$imageThumbnail.attr('src', imageThumbnailUri).show();
			}
		}

		var setIconType = function (iconType) {
			if (iconType !== null) {
				$iconType
					.removeClass('fa-__icon-type__')
					.addClass('fa-' + iconType)
					.show();
			}
		}
	}

})(jQuery);