(function ($){

	SS6 = SS6 || {};
	SS6.dragAndDropGrid = SS6.dragAndDropGrid || {};

	SS6.dragAndDropGrid.init = function () {
		$('.js-drag-and-drop-grid-rows').sortable({
			cursor: 'move',
			handle: '.js-drag-and-drop-grid-handle',
			items: '.js-grid-row',
			placeholder: 'table-drop'
		});

		$('.js-drag-and-drop-grid-submit').click(function () {
			var $grid = $(this).closest('.js-grid');

			var rows = $grid.find('.js-grid-row');

			var rowIds = [];
			$.each(rows, function(index, row) {
				rowIds.push($(row).data('drag-and-drop-grid-row-id'));
			});

			SS6.dragAndDropGrid.saveOrder($grid, rowIds);
		});
	};

	SS6.dragAndDropGrid.saveOrder = function ($grid, rowIds) {
		var data = {
			serviceName: $grid.data('drag-and-drop-ordering-service-name'),
			rowIds: rowIds
		};

		$.ajax({
			url: $grid.data('drag-and-drop-url-save-order'),
			type: 'POST',
			data: data,
			dataType: 'json',
			success: function () {
				SS6.window({content: 'Pořadí bylo uloženo'});
			}
		});
	};

	$(document).ready(function () {
		SS6.dragAndDropGrid.init();
	});

})(jQuery);