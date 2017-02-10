(function ($){

    Shopsys = Shopsys || {};
    Shopsys.grid = Shopsys.grid || {};
    Shopsys.grid.multipleDragAndDrop = Shopsys.grid.multipleDragAndDrop || {};

    Shopsys.grid.multipleDragAndDrop.init = function () {
        Shopsys.grid.multipleDragAndDrop.toggleRowHolders();
        $('.js-multiple-grids-save-all-button').click(Shopsys.grid.multipleDragAndDrop.saveOrdering);
        $('.js-multiple-grids-rows-unified').sortable({
            cursor: 'move',
            handle: '.cursor-move',
            items: '.js-grid-row, .js-grid-row-holder',
            placeholder: 'in-drop-place',
            revert: 200,
            change: Shopsys.grid.multipleDragAndDrop.onUpdate,
            update: Shopsys.grid.multipleDragAndDrop.onUpdate
        });

    };

    Shopsys.grid.multipleDragAndDrop.getPositionsIndexedByGridId = function ($grids) {
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

    Shopsys.grid.multipleDragAndDrop.saveOrdering = function (event) {
        var $saveButton = $(event.target);
        var $grids = $saveButton.closest('.js-multiple-grids-rows-unified').find('.js-grid');
        var data = {
            rowIdsByGridId: Shopsys.grid.multipleDragAndDrop.getPositionsIndexedByGridId($grids)
        };

        Shopsys.ajax({
            loaderElement: '.js-multiple-grids-save-all-button',
            url: $saveButton.data('drag-and-drop-url-save-ordering'),
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function () {
                Shopsys.window({content: Shopsys.translator.trans('Order saved')});
            },
            error: function () {
                Shopsys.window({content: Shopsys.translator.trans('Order saving failed')});
            }
        });

        $saveButton.addClass('btn--disabled');
    };

    Shopsys.grid.multipleDragAndDrop.onUpdate = function (event, ui) {
        $('.js-multiple-grids-save-all-button').removeClass('btn--disabled');
        Shopsys.grid.multipleDragAndDrop.toggleRowHolders();
    };

    Shopsys.grid.multipleDragAndDrop.toggleRowHolders = function () {
         $('.js-multiple-grids-rows-unified .js-grid').each(function() {
            var gridRowsCount = $(this).find('.js-grid-row:not(.ui-sortable-helper):not(.js-grid-row-holder), .in-drop-place').length;
            var $rowHolder = $(this).find('.js-grid-row-holder');
            $rowHolder.toggle(gridRowsCount === 0);
        });
    };

    $(document).ready(function () {
        Shopsys.grid.multipleDragAndDrop.init();
    });

})(jQuery);
