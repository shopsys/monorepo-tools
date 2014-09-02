(function ($){

	SS6 = SS6 || {};
	SS6.pkgrid = SS6.pkgrid || {};
	
	SS6.pkgrid.init = function (formElement) {
		$('.js-pkgrid-go-to').each(SS6.pkgrid.bindGoTo);
	}
	
	SS6.pkgrid.bindGoTo = function () {
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
		SS6.pkgrid.init();
	});
	
})(jQuery);