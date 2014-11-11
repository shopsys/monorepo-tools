(function ($){

	SS6 = window.SS6 || {};
	SS6.grid = SS6.grid || {};
	SS6.grid.inlineEdit = SS6.grid.inlineEdit || {};

	SS6.grid.inlineEdit.init = function () {
		$('.js-grid[data-inline-edit-service-name]').each(SS6.grid.inlineEdit.bind);
	}

	SS6.grid.inlineEdit.bind = function () {
		var $grid = $(this);

		$grid.on('click', '.js-inline-edit-edit', function() {
			var $row = $(this).closest('.js-grid-row');
			if (SS6.grid.inlineEdit.isRowEnabled($row)) {
				SS6.grid.inlineEdit.disableRow($row);
				SS6.grid.inlineEdit.startEditRow($row, $grid);
			}
			return false;
		});

		$grid.on('click', '.js-inline-edit-add', function() {
			$grid.find('.js-inline-edit-no-data').remove();
			$grid.find('.js-inline-edit-data-container').removeClass('hidden');
			SS6.grid.inlineEdit.addNewRow($grid);
		});

		$grid.on('click', '.js-inline-edit-cancel', function() {
			var $formRow = $(this).closest('.js-grid-editing-row');
			SS6.window({
				content: 'Opravdu chcete zahodit všechny změny?',
				buttonClose: true,
				buttonContinue: true,
				textContinue: 'Ano',
				eventContinue: function () {
					SS6.grid.inlineEdit.cancelEdit($formRow);
				}
			});
			return false;
		});

		$grid.on('click', '.js-inline-edit-save', function() {
			SS6.grid.inlineEdit.saveRow($(this).closest('.js-grid-editing-row'), $grid);
			return false;
		});

		$grid.on('keyup', '.js-grid-editing-row input', function(event) {
			if (event.keyCode == 13) { // enter
				SS6.grid.inlineEdit.saveRow($(this).closest('.js-grid-editing-row'), $grid);
			}
			return false;
		});

	}

	SS6.grid.inlineEdit.saveRow = function ($formRow, $grid) {
		var $buttons = $formRow.find('.js-inline-edit-buttons').hide();
		var $saving = $formRow.find('.js-inline-edit-saving').show();
		var $virtualForm = $('<form>')
				.append($formRow.clone())
				.append($('<input type="hidden" name="serviceName" />').val($grid.data('inline-edit-service-name')))
				.append($('<input type="hidden "name="themeJson" />').val($grid.data('inline-edit-theme-json')))

		var $originalRow = $formRow.data('$originalRow');
		if ($originalRow) {
			$virtualForm.append($('<input type="hidden" name="rowId" />').val($originalRow.data('inline-edit-row-id')))
			$originalRow.data('inline-edit-row-id');
		}

		$.ajax({
			url: $grid.data('inline-edit-url-save-form'),
			type: 'POST',
			data: $virtualForm.serialize(),
			dataType: 'json',
			success: function (saveResult) {
				if (saveResult.success) {
					$formRow.replaceWith($(saveResult.rowHtml)).remove();
					SS6.ajaxConfirm.init();
				} else {
					$buttons.show();
					$saving.hide();
					SS6.window({
						content: 'Prosím překontrolujte následující informace:\n\n• ' + saveResult.errors.join('\n• ')
					});
				}
			}
		});
	}

	SS6.grid.inlineEdit.startEditRow = function ($row, $grid) {
		$.ajax({
			url: $grid.data('inline-edit-url-get-form'),
			type: 'POST',
			data: {
				serviceName: $grid.data('inline-edit-service-name'),
				rowId: $row.data('inline-edit-row-id')
			},
			dataType: 'json',
			success: function (formData) {
				var $formRow = SS6.grid.inlineEdit.createFormRow($grid, formData);
				$formRow.find('.js-inline-edit-saving').hide();
				$row.replaceWith($formRow);
				$formRow.data('$originalRow', $row);
			}
		});
	}

	SS6.grid.inlineEdit.addNewRow = function ($grid) {
		$.ajax({
			url: $grid.data('inline-edit-url-get-form'),
			type: 'POST',
			data: {
				serviceName: $grid.data('inline-edit-service-name')
			},
			dataType: 'json',
			success: function (formData) {
				var $formRow = SS6.grid.inlineEdit.createFormRow($grid, formData);
				$formRow.find('.js-inline-edit-saving').hide();
				$grid.find('.js-inline-edit-rows').prepend($formRow);
			}
		});
	}

	SS6.grid.inlineEdit.cancelEdit = function ($formRow) {
		var $originalRow = $formRow.data('$originalRow');
		if ($originalRow) {
			$formRow.replaceWith($originalRow).remove();
			SS6.grid.inlineEdit.enableRow($originalRow);
		}
		$formRow.remove();
	}

	SS6.grid.inlineEdit.disableRow = function ($row) {
		return $row.addClass('js-inactive');
	}

	SS6.grid.inlineEdit.enableRow = function ($row) {
		return $row.removeClass('js-inactive');
	}

	SS6.grid.inlineEdit.isRowEnabled = function ($row) {
		return !$row.hasClass('js-inactive');
	}

	SS6.grid.inlineEdit.createFormRow = function ($grid, formData) {
		var $formRow = $grid.find('.js-grid-empty-row').clone();
		$formRow.removeClass('js-grid-empty-row hidden').addClass('js-grid-editing-row');
		var $otherInputs = $formRow.find('.js-inline-edit-other-inputs');

		$.each(formData, function(formName, formHtml) {
			var $column = $formRow.find('.js-grid-column-' + formName + ':first');
			if ($column.size() == 1) {
				$column.html(formHtml);
			} else {
				$(formHtml).prependTo($otherInputs);
			}
		});

		return $formRow;
	}

	$(document).ready(function () {
		SS6.grid.inlineEdit.init();
	});

})(jQuery);
