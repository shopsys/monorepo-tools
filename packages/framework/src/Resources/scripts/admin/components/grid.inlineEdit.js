(function ($) {

    Shopsys = window.Shopsys || {};
    Shopsys.grid = Shopsys.grid || {};
    Shopsys.grid.inlineEdit = Shopsys.grid.inlineEdit || {};

    Shopsys.grid.inlineEdit.init = function () {
        $('.js-grid[data-inline-edit-service-name]').each(Shopsys.grid.inlineEdit.bind);
    };

    Shopsys.grid.inlineEdit.bind = function () {
        var $grid = $(this);

        $grid.on('click', '.js-inline-edit-edit', function () {
            var $row = $(this).closest('.js-grid-row');
            if (Shopsys.grid.inlineEdit.isRowEnabled($row)) {
                Shopsys.grid.inlineEdit.disableRow($row);
                Shopsys.grid.inlineEdit.startEditRow($row, $grid);
            }
            return false;
        });

        $grid.on('click', '.js-inline-edit-add', function () {
            $grid.find('.js-inline-edit-no-data').remove();
            $grid.find('.js-inline-edit-data-container').removeClass('hidden');
            Shopsys.grid.inlineEdit.addNewRow($grid);
            return false;
        });

        $grid.on('click', '.js-inline-edit-cancel', function () {
            var $formRow = $(this).closest('.js-grid-editing-row');
            Shopsys.window({
                content: Shopsys.translator.trans('Do you really want to discard all changes?'),
                buttonCancel: true,
                buttonContinue: true,
                textContinue: Shopsys.translator.trans('Yes'),
                eventContinue: function () {
                    Shopsys.grid.inlineEdit.cancelEdit($formRow);
                }
            });
            return false;
        });

        $grid.on('click', '.js-inline-edit-save', function () {
            Shopsys.grid.inlineEdit.saveRow($(this).closest('.js-grid-editing-row'), $grid);
            $grid.find('.js-drag-and-drop-grid-rows').sortable('enable');
            return false;
        });

        $grid.on('keyup', '.js-grid-editing-row input', function (event) {
            if (event.keyCode == Shopsys.keyCodes.ENTER) {
                Shopsys.grid.inlineEdit.saveRow($(this).closest('.js-grid-editing-row'), $grid);
            }
            return false;
        });

    };

    Shopsys.grid.inlineEdit.saveRow = function ($formRow, $grid) {
        var $buttons = $formRow.find('.js-inline-edit-buttons').hide();
        var $saving = $formRow.find('.js-inline-edit-saving').show();
        var $virtualForm = $('<form>')
            .append($formRow.clone())
            .append($('<input type="hidden" name="serviceName" />')
                .val($grid.data('inline-edit-service-name')));

        var $originalRow = $formRow.data('$originalRow');
        if ($originalRow) {
            $virtualForm.append($('<input type="hidden" name="rowId" />').val($originalRow.data('inline-edit-row-id')));
            $originalRow.data('inline-edit-row-id');
        }

        Shopsys.ajax({
            url: $grid.data('inline-edit-url-save-form'),
            type: 'POST',
            data: $virtualForm.serialize(),
            dataType: 'json',
            success: function (saveResult) {
                if (saveResult.success) {
                    var $newRow = $(saveResult.rowHtml);
                    $formRow.replaceWith($newRow).remove();
                    Shopsys.register.registerNewContent($newRow);
                } else {
                    $buttons.show();
                    $saving.hide();
                    Shopsys.window({
                        content: Shopsys.translator.trans('Please check following information:') + '<br/><br/>• ' + saveResult.errors.join('<br/>• ')
                    });
                }
            },
            error: function () {
                Shopsys.window({
                    content: Shopsys.translator.trans('Error occurred, try again please.')
                });
                $buttons.show();
                $saving.hide();
            }
        });
    };

    Shopsys.grid.inlineEdit.startEditRow = function ($row, $grid) {
        Shopsys.ajax({
            url: $grid.data('inline-edit-url-get-form'),
            type: 'POST',
            data: {
                serviceName: $grid.data('inline-edit-service-name'),
                rowId: $row.data('inline-edit-row-id')
            },
            dataType: 'json',
            success: function (formRowData) {
                var $formRow = $($.parseHTML(formRowData));
                $formRow.addClass('js-grid-editing-row');
                $formRow.find('.js-inline-edit-saving').hide();
                $row.replaceWith($formRow);
                Shopsys.register.registerNewContent($formRow);
                $formRow.data('$originalRow', $row);
            }
        });
    };

    Shopsys.grid.inlineEdit.addNewRow = function ($grid) {
        Shopsys.ajax({
            url: $grid.data('inline-edit-url-get-form'),
            type: 'POST',
            data: {
                serviceName: $grid.data('inline-edit-service-name')
            },
            dataType: 'json',
            success: function (formRowData) {
                var $formRow = $($.parseHTML(formRowData));
                $formRow.addClass('js-grid-editing-row');
                $formRow.find('.js-inline-edit-saving').hide();
                Shopsys.register.registerNewContent($formRow);
                $grid.find('.js-inline-edit-rows').prepend($formRow);
                $formRow.find('input[type=text]:first').focus();
                $grid.find('.js-drag-and-drop-grid-rows').sortable('disable');
            }
        });
    };

    Shopsys.grid.inlineEdit.cancelEdit = function ($formRow) {
        var $originalRow = $formRow.data('$originalRow');
        if ($originalRow) {
            $formRow.replaceWith($originalRow).remove();
            Shopsys.register.registerNewContent($originalRow);
            Shopsys.grid.inlineEdit.enableRow($originalRow);
        }
        $formRow.remove();
    };

    Shopsys.grid.inlineEdit.disableRow = function ($row) {
        return $row.addClass('js-inactive');
    };

    Shopsys.grid.inlineEdit.enableRow = function ($row) {
        return $row.removeClass('js-inactive');
    };

    Shopsys.grid.inlineEdit.isRowEnabled = function ($row) {
        return !$row.hasClass('js-inactive');
    };

    $(document).ready(function () {
        Shopsys.grid.inlineEdit.init();
    });

})(jQuery);
