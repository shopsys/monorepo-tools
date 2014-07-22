(function ($) {

	var containerIdDataAttribute = 'checkbox-toggle-container-id';

	$.fn.SS6 = $.fn.SS6 || {};
	$.fn.SS6.checkboxToggle = $.fn.SS6.checkboxToggle || {};
	
	$.fn.SS6.checkboxToggle.init = function () {
		$('.checkbox-toggle').on('change', $.fn.SS6.checkboxToggle.onChange);
		
		$('.checkbox-toggle').each(function () {
			var containerId = $(this).data(containerIdDataAttribute);
		
			if ($(this).is(':checked')) {
				$('#' + containerId).show();
			} else {
				$('#' + containerId).hide();
			}
		});
	};
	
	$.fn.SS6.checkboxToggle.onChange = function (event) {
		var containerId = $(this).data(containerIdDataAttribute);
		
		if ($(this).is(':checked')) {
			$('#' + containerId).slideDown('fast');
		} else {
			$('#' + containerId).slideUp('fast');
		}
	};
	
	$(document).ready(function () {
		$.fn.SS6.checkboxToggle.init();
	});
	
})(jQuery);
