(function ($){

	SS6 = window.SS6 || {};
	SS6.grid = SS6.grid || {};
	SS6.grid.inlineEdit = SS6.grid.inlineEdit || {};

	SS6.grid.inlineEdit.init = function () {
		$('.js-grid[data-inline-edit-service-name]').each(SS6.grid.inlineEdit.bind);
	};

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
			return false;
		});

		$grid.on('click', '.js-inline-edit-cancel', function() {
			var $formRow = $(this).closest('.js-grid-editing-row');
			SS6.window({
				content: 'Opravdu chcete zahodit všechny změny?',
				buttonCancel: true,
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
			if (event.keyCode == SS6.keyCodes.ENTER) {
				SS6.grid.inlineEdit.saveRow($(this).closest('.js-grid-editing-row'), $grid);
			}
			return false;
		});

	};

	SS6.grid.inlineEdit.saveRow = function ($formRow, $grid) {
		var $buttons = $formRow.find('.js-inline-edit-buttons').hide();
		var $saving = $formRow.find('.js-inline-edit-saving').show();
		var $virtualForm = $('<form>')
				.append($formRow.clone())
				.append($('<input type="hidden" name="serviceName" />').val($grid.data('inline-edit-service-name')));

		var $originalRow = $formRow.data('$originalRow');
		if ($originalRow) {
			$virtualForm.append($('<input type="hidden" name="rowId" />').val($originalRow.data('inline-edit-row-id')));
			$originalRow.data('inline-edit-row-id');
		}

		SS6.ajax({
			url: $grid.data('inline-edit-url-save-form'),
			type: 'POST',
			data: $virtualForm.serialize(),
			dataType: 'json',
			success: function (saveResult) {
				if (saveResult.success) {
					var $newRow = $(saveResult.rowHtml);
					$formRow.replaceWith($newRow).remove();
					SS6.register.registerNewContent($newRow);
				} else {
					$buttons.show();
					$saving.hide();
					SS6.window({
						content: 'Prosím překontrolujte následující informace:<br/><br/>• ' + saveResult.errors.join('<br/>• ')
					});
				}
			},
			error: function () {
				SS6.window({
					content: SS6.translator.trans('Nastala chyba, zkuste to, prosím, znovu.')
				});
				$buttons.show();
				$saving.hide();
			}
		});
	};

	SS6.grid.inlineEdit.startEditRow = function ($row, $grid) {
		SS6.ajax({
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
				SS6.register.registerNewContent($formRow);
				$formRow.data('$originalRow', $row);
			}
		});
	};

	SS6.grid.inlineEdit.addNewRow = function ($grid) {
		SS6.ajax({
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
				SS6.register.registerNewContent($formRow);
				$grid.find('.js-inline-edit-rows').prepend($formRow);
				$formRow.find('input[type=text]:first').focus();
			}
		});
	};

	SS6.grid.inlineEdit.cancelEdit = function ($formRow) {
		var $originalRow = $formRow.data('$originalRow');
		if ($originalRow) {
			$formRow.replaceWith($originalRow).remove();
			SS6.register.registerNewContent($originalRow);
			SS6.grid.inlineEdit.enableRow($originalRow);
		}
		$formRow.remove();
	};

	SS6.grid.inlineEdit.disableRow = function ($row) {
		return $row.addClass('js-inactive');
	};

	SS6.grid.inlineEdit.enableRow = function ($row) {
		return $row.removeClass('js-inactive');
	};

	SS6.grid.inlineEdit.isRowEnabled = function ($row) {
		return !$row.hasClass('js-inactive');
	};

	$(document).ready(function () {
		SS6.grid.inlineEdit.init();
	});

})(jQuery);
