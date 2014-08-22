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
	
	SS6.pkgrid.inlineEdit.disableRow = function ($row) {
		return $row.addClass('js-inactive');
	}
	
	SS6.pkgrid.inlineEdit.enableRow = function ($row) {
		return $row.addClass('js-inactive');
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