(function ($){

	SS6 = SS6 || {};
	SS6.grid = SS6.grid || {};
	SS6.grid.multipleDragAndDrop = SS6.grid.multipleDragAndDrop || {};

	SS6.grid.multipleDragAndDrop.init = function () {
		SS6.grid.multipleDragAndDrop.toggleRowHolders();
		$('.js-multiple-grids-save-all-button').click(SS6.grid.multipleDragAndDrop.saveOrdering);
		$('.js-multiple-grids-rows-unified').sortable({
			cursor: 'move',
			handle: '.h-cursor-move',
			items: '.js-grid-row, .js-grid-row-holder',
			placeholder: 'in-drop-place',
			revert: 200,
			change: SS6.grid.multipleDragAndDrop.onUpdate,
			update: SS6.grid.multipleDragAndDrop.onUpdate
		});

	};

	SS6.grid.multipleDragAndDrop.getPositionsIndexedByGridId = function ($grids) {
		var rowIdsIndexedByGridId = {};
		$.each($grids, function(index, grid) {
			var $grid = $(grid);
			var gridId = $grid.data('grid-id');
			rowIdsIndexedByGridId[gridId] = {};
			var rows = $grid.find('.js-grid-row');

			$.each(rows, function(rowIndex, row) {
				rowIdsIndexedByGridId[gridId][rowIndex] = $(row).data('drag-and-drop-grid-row-id');
			});
		});

		return rowIdsIndexedByGridId;
	};

	SS6.grid.multipleDragAndDrop.saveOrdering = function (event) {
		var $saveButton = $(event.target);
		var $grids = $saveButton.closest('.js-multiple-grids-rows-unified').find('.js-grid');
		var data = {
			rowIdsByGridId: SS6.grid.multipleDragAndDrop.getPositionsIndexedByGridId($grids)
		};

		SS6.ajax({
			loaderElement: '.js-multiple-grids-save-all-button',
			url: $saveButton.data('drag-and-drop-url-save-ordering'),
			type: 'POST',
			data: data,
			dataType: 'json',
			success: function () {
				SS6.window({content: SS6.translator.trans('Pořadí bylo uloženo')});
			},
			error: function () {
				SS6.window({content: SS6.translator.trans('Pořadí se nepodařilo uložit')});
			}
		});

		$saveButton.addClass('btn--disabled');
	};

	SS6.grid.multipleDragAndDrop.onUpdate = function (event, ui) {
		$('.js-multiple-grids-save-all-button').removeClass('btn--disabled');
		SS6.grid.multipleDragAndDrop.toggleRowHolders();
	};

	SS6.grid.multipleDragAndDrop.toggleRowHolders = function () {
		 $('.js-multiple-grids-rows-unified .js-grid').each(function() {
			var gridRowsCount = $(this).find('.js-grid-row:not(.ui-sortable-helper):not(.js-grid-row-holder), .in-drop-place').size();
			var $rowHolder = $(this).find('.js-grid-row-holder');
			$rowHolder.toggle(gridRowsCount === 0);
		});
	};

	$(document).ready(function () {
		SS6.grid.multipleDragAndDrop.init();
	});

})(jQuery);
