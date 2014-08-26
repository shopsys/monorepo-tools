(function ($){
	
	SS6 = window.SS6 || {};
	SS6.pkgrid = SS6.pkgrid || {};
	SS6.pkgrid.inlineEdit = SS6.pkgrid.inlineEdit || {};
	
	SS6.pkgrid.inlineEdit.init = function (formElement) {
		$('.js-pkgrid[data-inline-edit-service-name]').each(SS6.pkgrid.inlineEdit.bind);
	}
	
	SS6.pkgrid.inlineEdit.bind = function () {
		var $grid = $(this);
		
		$grid.on('click', '.js-pkgrid-edit', function() {
			var $row = $(this).closest('.js-pkgrid-row');
			if (SS6.pkgrid.inlineEdit.isEnableRow($row)) {
				SS6.pkgrid.inlineEdit.disableRow($row);
				SS6.pkgrid.inlineEdit.startEditRow($row, $grid);
			}
		});
		
		$grid.on('click', '.js-pkgrid-cancel', function() {
			var $formRow = $(this).closest('.js-pkgrid-editing-row');
			if (confirm('Opravdu chcete zahodit všechny změny?')) {
				SS6.pkgrid.inlineEdit.cancelEdit($formRow);
			}
		});
		
		$grid.on('click', '.js-pkgrid-save', function() {
			SS6.pkgrid.inlineEdit.saveRow($(this).closest('.js-pkgrid-editing-row'), $grid);
		});
		
	}
	
	SS6.pkgrid.inlineEdit.saveRow = function ($formRow, $grid) {
		var $originRow = $formRow.data('$originRow');
		var data = $('<form>')
				.append($formRow.clone())
				.append($('<input type="hidden" name="rowId" />').val($originRow.data('inline-edit-row-id')))
				.append($('<input type="hidden" name="serviceName" />').val($grid.data('inline-edit-service-name')))
				.append($('<input type="hidden "name="themeJson" />').val($grid.data('inline-edit-theme-json')))
			.serialize();
		$.ajax({
			url: $grid.data('inline-edit-url-save-form'),
			type: 'POST',
			data: data,
			dataType: 'json',
			success: function (saveResult) {
				if (saveResult.success) {
					$formRow.data('$originRow').replaceWith($($(saveResult.rowHtml)));
					$formRow.remove();
				} else {
					alert('Prosím překontrolujte následující informace:\n\n• ' + saveResult.errors.join('\n• '));
				}
			}
		});
	}
	
	SS6.pkgrid.inlineEdit.startEditRow = function ($row, $grid) {
		$.ajax({
			url: $grid.data('inline-edit-url-get-form'),
			type: 'POST',
			data: {
				serviceName: $grid.data('inline-edit-service-name'),
				rowId: $row.data('inline-edit-row-id')
			},
			dataType: 'json',
			success: function (formData) {
				var $formRow = SS6.pkgrid.inlineEdit.createFormRow($grid, formData);
				$formRow.insertAfter($row);
				$formRow.data('$originRow', $row);
				$row.hide();
			}
		});
	}
	
	SS6.pkgrid.inlineEdit.cancelEdit = function ($formRow) {
		var $originRow = $formRow.data('$originRow');
		$originRow.show();
		SS6.pkgrid.inlineEdit.enableRow($originRow);
		$formRow.remove();
	}
	
	SS6.pkgrid.inlineEdit.disableRow = function ($row) {
		return $row.addClass('js-inactive');
	}
	
	SS6.pkgrid.inlineEdit.enableRow = function ($row) {
		return $row.removeClass('js-inactive');
	}
	
	SS6.pkgrid.inlineEdit.isEnableRow = function ($row) {
		return !$row.hasClass('js-inactive');
	}
	
	SS6.pkgrid.inlineEdit.createFormRow = function ($grid, formData) {
		var $formRow = $grid.find('.js-pkgrid-empty-row').clone();
		$formRow.removeClass('js-pkgrid-empty-row hidden').addClass('js-pkgrid-editing-row');
		var $otherInputs = $formRow.find('.js-inline-edit-other-inputs');

		$.each(formData, function(formName, formHtml) {
			var $column = $formRow.find('.js-pkgrid-column-' + formName + ':first');
			if ($column.size() == 1) {
				$column.html(formHtml);
			} else {
				$(formHtml).prependTo($otherInputs);
			}
		});
		
		return $formRow;
	}
	
	$(document).ready(function () {
		SS6.pkgrid.inlineEdit.init();
	});
	
})(jQuery);