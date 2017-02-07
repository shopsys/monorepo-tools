(function ($){

	SS6 = SS6 || {};
	SS6.grid = SS6.grid || {};
	SS6.grid.dragAndDrop = SS6.grid.dragAndDrop || {};

	SS6.grid.dragAndDrop.init = function () {
		$('.js-drag-and-drop-grid-rows').sortable({
			create: SS6.grid.dragAndDrop.onCreate,
			cursor: 'move',
			handle: '.cursor-move',
			items: '.js-grid-row',
			placeholder: 'in-drop-place',
			revert: 200,
			update: SS6.grid.dragAndDrop.onUpdate
		});

		$('.js-grid').each(function () {
			var $grid = $(this);
			SS6.grid.dragAndDrop.initGrid($grid);
		});

		SS6.grid.dragAndDrop.unifyMultipleGrids();
	};

	SS6.grid.dragAndDrop.initGrid = function ($grid) {
		$grid.find('.js-drag-and-drop-grid-submit').click(function () {
			if (!$grid.data('positionsChanged')) {
				return false;
			}

			SS6.grid.dragAndDrop.saveOrdering($grid);
		});

		$grid.data('positionsChanged', false);
		SS6.grid.dragAndDrop.highlightChanges($grid, false);
	};

	SS6.grid.dragAndDrop.getPositions = function ($grid) {
		var rows = $grid.find('.js-grid-row');

		var rowIds = [];
		$.each(rows, function(index, row) {
			rowIds.push($(row).data('drag-and-drop-grid-row-id'));
		});

		return rowIds;
	};

	SS6.grid.dragAndDrop.saveOrdering = function ($grid, rowIds) {
		var data = {
			entityClass: $grid.data('drag-and-drop-ordering-entity-class'),
			rowIds: SS6.grid.dragAndDrop.getPositions($grid)
		};

		SS6.ajax({
			loaderElement: '.js-drag-and-drop-grid-submit, js-drag-and-drop-grid-submit-all',
			url: $grid.data('drag-and-drop-url-save-ordering'),
			type: 'POST',
			data: data,
			dataType: 'json',
			success: function () {
				$grid.data('positionsChanged', false);
				SS6.grid.dragAndDrop.highlightChanges($grid, false);

				SS6.window({content: SS6.translator.trans('Order saved')});
			},
			error: function () {
				SS6.window({content: SS6.translator.trans('Order saving failed')});
			}
		});
		$grid.trigger('save');
	};

	SS6.grid.dragAndDrop.onUpdate = function (event, ui) {
		var $grid = $(event.target).closest('.js-grid');

		$grid.data('positionsChanged', true);
		SS6.grid.dragAndDrop.highlightChanges($grid, true);
		$grid.trigger('update');
	};

	SS6.grid.dragAndDrop.highlightChanges = function ($grid, highlight) {
		if (highlight) {
			$grid.find('.js-drag-and-drop-grid-submit').removeClass('btn--disabled');
		} else {
			$grid.find('.js-drag-and-drop-grid-submit').addClass('btn--disabled');
		}
	};

	SS6.grid.dragAndDrop.unifyMultipleGrids = function () {
		var $gridSaveButtons = $('.js-drag-and-drop-grid-submit');
		var $gridsOnPage = $('.js-grid[data-drag-and-drop-ordering-entity-class]');
		var $saveAllButton = $('.js-drag-and-drop-grid-submit-all');

		if ($saveAllButton.length === 1) {
			$gridSaveButtons.hide();

			$gridsOnPage.on('update', function() {
				SS6.formChangeInfo.showInfo();
				$saveAllButton.removeClass('btn--disabled');
			});

			$gridsOnPage.on('save', function() {
				SS6.formChangeInfo.removeInfo();
				$saveAllButton.addClass('btn--disabled');
			});

			$saveAllButton.click(function() {
				$gridSaveButtons.click();
			});
		}
	};

	$(document).ready(function () {
		SS6.grid.dragAndDrop.init();
	});

})(jQuery);
