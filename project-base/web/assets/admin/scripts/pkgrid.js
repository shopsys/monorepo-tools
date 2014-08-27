(function ($){
	$.fn.SS6 = $.fn.SS6 || {};
	$.fn.SS6.pkgrid = $.fn.SS6.pkgrid || {};
	
	$.fn.SS6.pkgrid.init = function (formElement) {
		$('.js-pkgrid-go-to').each($.fn.SS6.pkgrid.bindGoTo);
	}
	
	$.fn.SS6.pkgrid.bindGoTo = function () {
		var $button = $(this).find('.js-pkgrid-go-to-button');
		var $input = $(this).find('.js-pkgrid-go-to-input');
		
		$input.bind('keyup.pkgridGoTo', function (event) {
			if (event.keyCode == 13) {
				$button.trigger('click.pkgridGoTo', event);
			}
		});
		
		$button.bind('click.pkgridGoTo', function () {
			document.location = $(this).data('url').replace('--page--', $input.val());
			return false;
		});
	}
	
	$(document).ready(function () {
		$.fn.SS6.pkgrid.init();
	});
	
})(jQuery);