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
			SS6.pkgrid.inlineEdit.startEditRow($row, $grid);
		});
	}
	
	SS6.pkgrid.inlineEdit.startEditRow = function ($row, $grid) {
		$.ajax({
			url: '/admin/_pkgrid/get_form/', // TODO: rewrite to url from PHP Router
			type: 'POST',
			data: {
				serviceName: $grid.data('inline-edit-service-name'),
				rowId: $row.data('inline-edit-row-id')
			},
			dataType: 'json',
			success: function (formData) {
				var $rowWithForm = SS6.pkgrid.inlineEdit.createRowWithForm($grid, formData);
			}
		});
	}
	
	SS6.pkgrid.inlineEdit.createRowWithForm = function ($grid, formData) {
		
	}
	
	$(document).ready(function () {
		SS6.pkgrid.inlineEdit.init();
	});
	
})(jQuery);