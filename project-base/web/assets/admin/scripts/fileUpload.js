(function ($) {

	SS6 = window.SS6 || {};
	SS6.fileUpload = SS6.fileUpload || {};

	SS6.fileUpload.init = function () {
		$('.js-file-upload').each(function() {
			new SS6.fileUpload.uploader($(this));
		});
	}

	SS6.fileUpload.uploader = function ($uploader) {
		var uploader = this;
		this.$uploader = $uploader;
		this.$item = $uploader.find('.js-file-upload-item');
		this.$uploadedFiles = $uploader.find('.js-file-upload-uploaded-files');
		this.$uploadedFileTemplate = $($.parseHTML(this.$uploadedFiles.data('prototype').replace(/__name__/g, '')));
		this.$status = $uploader.find('.js-file-upload-status');
		this.$fallbackHide = $uploader.find('.js-file-upload-fallback-hide');
		this.multiple = $uploader.find('input[type=file]').attr('multiple') === 'multiple';
		this.ready = true;
		this.items = [];
		this.lastUploadItemId = null;

		this.$uploadedFileTemplate.find('*[id]').removeAttr('id');
		this.$uploadedFileTemplate.html(this.$uploadedFileTemplate.html());

		var updateFileStatus = function (status, message) {
			uploader.$status.parent().stop(true, true).show();
			uploader.$status.text(message).removeClass('error success uploading').addClass(status);
		}

		var onFormSubmit = function (event) {
			if (!uploader.ready) {
				SS6.window({
					content: 'Prosím počkejte dokud nebudou nahrány všechny soubory a zkuste to znovu.'
				});
				event.preventDefault();
			}
		}

		this.tryDeleteTemporaryFile = function (filename) {
			$.ajax({
				url: uploader.$uploader.data('fileupload-delete-url'),
				type: 'POST',
				data: {filename: filename},
				dataType: 'json'
			});
		}

		this.removeUploadedFile = function() {
			SS6.fileUpload.tryDeleteTemporaryFile(uploader);
			uploader.$hiddenInput.val('');
			uploader.$item.hide();
		}

		$uploader.closest('form').bind('submit.file-upload', onFormSubmit);

		var fileItem = function ($file) {
			var $label = $file.find('.js-file-upload-label');
			var $delete = $file.find('.js-file-upload-delete');
			var $progress = $file.find('.js-file-upload-progress');
			var $progressBar = $file.find('.js-file-upload-progress-bar');
			var $input = $file.find('.js-file-upload-input');

			$progress.hide();

			this.setAsUploaded = function (filename) {
				$input.val(filename);
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
		}

		$uploader.dmUploader({
			url: $uploader.data('fileupload-url'),
			dataType: 'json',
			onBeforeUpload: function(id){
				uploader.ready = false;
				updateFileStatus('uploading', 'Nahrávám...');
			},
			onNewFile: function(id, file){
				var $file = uploader.$uploadedFileTemplate.clone();
				$file.show();
				uploader.items[id] = new fileItem($file);
				uploader.items[id].setLabel(file.name, file.size);
				uploader.$uploadedFiles.append($file);
			},
			onComplete: function(){
				uploader.ready = true;
			},
			onUploadProgress: function(id, percent){
				uploader.items[id].setProgress(percent);
				updateFileStatus('uploading', 'Nahrávám...');
			},
			onUploadSuccess: function(id, data){
				if (data.status === 'success') {
					if (uploader.lastUploadItemId !== null && uploader.multiple === false) {
						uploader.items[uploader.lastUploadItemId].deleteItem();
					}
					uploader.lastUploadItemId = id;
					uploader.items[id].setAsUploaded(data.filename);
					updateFileStatus('success', 'Úspěšně nahráno');
					uploader.$status.parent().fadeOut(4000);
				} else {
					uploader.items[id].deleteItem();
					SS6.window({
						content: 'Při nahrávání souboru došlo k chybě.'
					});
				}
			},
			onUploadError: function(id, message){
				uploader.items[id].deleteItem();
				SS6.window({
					content: 'Při nahrávání souboru došlo k chybě: ' + message
				});
			},
			onFileTypeError: function(file){

			},
			onFileSizeError: function(file){

			},
			onFallbackMode: function(message){
				uploader.$fallbackHide.hide();
			}
		});
	}

	$(document).ready(function () {
		SS6.fileUpload.init();
	});

})(jQuery);
