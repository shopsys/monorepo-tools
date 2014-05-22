(function ($) {

	$.fn.SS6 = $.fn.SS6 || {};
	$.fn.SS6.fileUpload = $.fn.SS6.fileUpload || {};
	
	$.fn.SS6.fileUpload.init = function () {
		$('.js-file-upload').each(function() {
			new $.fn.SS6.fileUpload.uploader($(this));
		});
	}
	
	$.fn.SS6.fileUpload.tryDeleteCachedFile = function (uploader) {
		var filename = uploader.$hiddenInput.val();
		if (filename) {
			$.ajax({
				url: uploader.$uploader.data('fileupload-delete-url'),
				type: 'POST',
				data: {filename: filename},
				dataType: 'json'
			});
		}
	}
	
	$.fn.SS6.fileUpload.uploader = function ($uploader) {
		var uploader = this;
		this.$uploader = $uploader;
		this.$item = $uploader.find('.js-file-upload-item');
		this.$hiddenInput = $uploader.find('.js-file-upload-hidden input:first');
		this.$filename = $uploader.find('.js-file-upload-filename');
		this.$filenameRemove = $uploader.find('.js-file-upload-filename-delete');
		this.$status = $uploader.find('.js-file-upload-status');
		this.$progress = $uploader.find('.js-file-upload-progress');
		this.$progressBar = $uploader.find('.js-file-upload-progress-bar');
		this.$fallbackHide = $uploader.find('.js-file-upload-fallback-hide');
		this.ready = true;
		this.lastName = '';

		var updateFileStatus = function (status, message) {
			uploader.$status.text(message).removeClass('error success uploading').addClass(status);
		}
		
		var onFormSubmit = function (event) {
			if (!uploader.ready) {
				alert('Prosím počkejte dokud nebudou nahrány všechny soubory a zkuste to znovu.');
				event.preventDefault();
			}
		}
		
		this.removeUploadedFile = function() {
			$.fn.SS6.fileUpload.tryDeleteCachedFile(uploader);
			uploader.$hiddenInput.val('');
			uploader.$item.hide();
		}
		
		this.$filenameRemove.bind('click', this.removeUploadedFile);
		
		$uploader.closest('form').bind('submit.file-upload', onFormSubmit);
		
		$uploader.dmUploader({
			url: $uploader.data('fileupload-url'),
			dataType: 'json',
			onBeforeUpload: function(id){
				uploader.$progress.show();
				uploader.ready = false;
				updateFileStatus('uploading', 'Nahrávám...');
			},
			onNewFile: function(id, file){
				uploader.lastName = uploader.$filename.text();
				uploader.id = id;
				uploader.$item.show().addClass('js-file-upload-state_uploading');
				var sizeInMB = Math.round(file.size / 1024 / 1024 * 100) / 100;
				uploader.$filename.text(file.name + ' (' + sizeInMB + ' MB)');
			},
			onComplete: function(){
				uploader.$progress.hide();
				uploader.ready = true;
			},
			onUploadProgress: function(id, percent){
				uploader.$progressBar.width(percent + '%').text(percent + '%');
			},
			onUploadSuccess: function(id, data){
				uploader.$progress.hide();
				if (data.status === 'success') {
					$.fn.SS6.fileUpload.tryDeleteCachedFile(uploader);
					uploader.$hiddenInput.val(data.filename);
					updateFileStatus('success', 'Úspěšně nahráno');
				} else {
					uploader.$filename.text(uploader.lastName);
				}
			},
			onUploadError: function(id, message){
				updateFileStatus('error', message);
				uploader.$filename.text(uploader.lastName);
			},
			onFileTypeError: function(file){
				
			},
			onFileSizeError: function(file){
	
			},
			onFallbackMode: function(message){
				if (!uploader.$hiddenInput.val()) {
					uploader.$fallbackHide.hide();
				}
			}
		});
	}
	
	$(document).ready(function () {
		$.fn.SS6.fileUpload.init();
	});
	
})(jQuery);
