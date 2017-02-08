(function ($){

	Shopsys = Shopsys || {};
	Shopsys.grid = Shopsys.grid || {};

	Shopsys.grid.init = function (formElement) {
		$('.js-grid-go-to').each(Shopsys.grid.bindGoTo);
	};

	Shopsys.grid.bindGoTo = function () {
		var $button = $(this).find('.js-grid-go-to-button');
		var $input = $(this).find('.js-grid-go-to-input');

		$input.bind('keydown.gridGoTo', function (event) {
			if (event.keyCode == Shopsys.keyCodes.ENTER) {
				$button.trigger('click.gridGoTo', event);

				return false;
			}
		});

		$button.bind('click.gridGoTo', function () {
			document.location = $(this).data('url').replace('--page--', $input.val());
			return false;
		});
	};

	$(document).ready(function () {
		Shopsys.grid.init();
	});

})(jQuery);
