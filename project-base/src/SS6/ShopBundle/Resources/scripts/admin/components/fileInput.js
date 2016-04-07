(function ($) {

	SS6 = window.SS6 || {};

	SS6.register.registerCallback(function ($container) {
		$container.filterAllNodes('input[type=file]').bootstrapFileInput();
		$container.filterAllNodes('.file-inputs').bootstrapFileInput();
	});

})(jQuery);