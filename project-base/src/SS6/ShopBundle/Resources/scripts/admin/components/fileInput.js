SS6 = window.SS6 || {};

SS6.register.registerCallback(function ($container) {
		$container.find('input[type=file]').bootstrapFileInput();
		$container.find('.file-inputs').bootstrapFileInput();
	});
