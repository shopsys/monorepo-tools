(function ($){

	Shopsys = Shopsys || {};
	Shopsys.grid = Shopsys.grid || {};
	Shopsys.grid.dragAndDrop = Shopsys.grid.dragAndDrop || {};

	Shopsys.grid.dragAndDrop.init = function () {
		$('.js-drag-and-drop-grid-rows').sortable({
			create: Shopsys.grid.dragAndDrop.onCreate,
			cursor: 'move',
			handle: '.cursor-move',
			items: '.js-grid-row',
			placeholder: 'in-drop-place',
			revert: 200,
			update: Shopsys.grid.dragAndDrop.onUpdate
		});

		$('.js-grid').each(function () {
			var $grid = $(this);
			Shopsys.grid.dragAndDrop.initGrid($grid);
		});

		Shopsys.grid.dragAndDrop.unifyMultipleGrids();
	};

	Shopsys.grid.dragAndDrop.initGrid = function ($grid) {
		$grid.find('.js-drag-and-drop-grid-submit').click(function () {
			if (!$grid.data('positionsChanged')) {
				return false;
			}

			Shopsys.grid.dragAndDrop.saveOrdering($grid);
		});

		$grid.data('positionsChanged', false);
		Shopsys.grid.dragAndDrop.highlightChanges($grid, false);
	};

	Shopsys.grid.dragAndDrop.getPositions = function ($grid) {
		var rows = $grid.find('.js-grid-row');

		var rowIds = [];
		$.each(rows, function(index, row) {
			rowIds.push($(row).data('drag-and-drop-grid-row-id'));
		});

		return rowIds;
	};

	Shopsys.grid.dragAndDrop.saveOrdering = function ($grid, rowIds) {
		var data = {
			entityClass: $grid.data('drag-and-drop-ordering-entity-class'),
			rowIds: Shopsys.grid.dragAndDrop.getPositions($grid)
		};

		Shopsys.ajax({
			loaderElement: '.js-drag-and-drop-grid-submit, js-drag-and-drop-grid-submit-all',
			url: $grid.data('drag-and-drop-url-save-ordering'),
			type: 'POST',
			data: data,
			dataType: 'json',
			success: function () {
				$grid.data('positionsChanged', false);
				Shopsys.grid.dragAndDrop.highlightChanges($grid, false);

				Shopsys.window({content: Shopsys.translator.trans('Order saved')});
			},
			error: function () {
				Shopsys.window({content: Shopsys.translator.trans('Order saving failed')});
			}
		});
		$grid.trigger('save');
	};

	Shopsys.grid.dragAndDrop.onUpdate = function (event, ui) {
		var $grid = $(event.target).closest('.js-grid');

		$grid.data('positionsChanged', true);
		Shopsys.grid.dragAndDrop.highlightChanges($grid, true);
		$grid.trigger('update');
	};

	Shopsys.grid.dragAndDrop.highlightChanges = function ($grid, highlight) {
		if (highlight) {
			$grid.find('.js-drag-and-drop-grid-submit').removeClass('btn--disabled');
		} else {
			$grid.find('.js-drag-and-drop-grid-submit').addClass('btn--disabled');
		}
	};

	Shopsys.grid.dragAndDrop.unifyMultipleGrids = function () {
		var $gridSaveButtons = $('.js-drag-and-drop-grid-submit');
		var $gridsOnPage = $('.js-grid[data-drag-and-drop-ordering-entity-class]');
		var $saveAllButton = $('.js-drag-and-drop-grid-submit-all');

		if ($saveAllButton.length === 1) {
			$gridSaveButtons.hide();

			$gridsOnPage.on('update', function() {
				Shopsys.formChangeInfo.showInfo();
				$saveAllButton.removeClass('btn--disabled');
			});

			$gridsOnPage.on('save', function() {
				Shopsys.formChangeInfo.removeInfo();
				$saveAllButton.addClass('btn--disabled');
			});

			$saveAllButton.click(function() {
				$gridSaveButtons.click();
			});
		}
	};

	$(document).ready(function () {
		Shopsys.grid.dragAndDrop.init();
	});

})(jQuery);
