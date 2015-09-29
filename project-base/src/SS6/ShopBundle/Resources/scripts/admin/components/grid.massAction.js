(function ($){

	SS6 = SS6 || {};
	SS6.grid = SS6.grid || {};
	SS6.grid.massAction = SS6.grid.massAction || {};

	SS6.grid.massAction = function ($grid) {
		var $selectAllCheckbox = $grid.find('.js-grid-mass-action-select-all');

		this.init = function () {
			$selectAllCheckbox.click(onSelectAll);
		};

		var onSelectAll = function () {
			$grid.find('.js-grid-mass-action-select-row').prop('checked', $selectAllCheckbox.is(':checked'));
		};

	};

	$(document).ready(function () {
		$('.js-grid').each(function () {
			var massAction = new SS6.grid.massAction($(this));
			massAction.init();
		});
	});

})(jQuery);
