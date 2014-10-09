(function ($) {

	SS6 = window.SS6 || {};
	SS6.orderStatusDelete = SS6.orderStatusDelete || {};
	
	SS6.orderStatusDelete.init = function () {
		$('#js-order-status-grid').on('click', '.js-order-status-delete-button', SS6.orderStatusDelete.onDelete);
		
		$(document).on('change', '#js-order-status-delete-status-select', function() {
			if ($(this).val() !== '') {
				SS6.orderStatusDelete.enableSubmit($('#js-order-status-delete-submit'));
			} else {
				SS6.orderStatusDelete.disableSubmit($('#js-order-status-delete-submit'));
			}
		});
	};
	
	SS6.orderStatusDelete.onDelete = function(event) {
		$.ajax({
			url: $(this).data('ajax-action'),
			type: 'POST',
			success: SS6.orderStatusDelete.onDeleteResponseSuccess
		});
		
		event.preventDefault();
		return false;
	};
	
	SS6.orderStatusDelete.onDeleteResponseSuccess = function(data) {
		$('#js-order-status-delete-window').html(data);
		SS6.window.open('orderStatusDelete');
		SS6.orderStatusDelete.disableSubmit($('#js-order-status-delete-submit'));
	};
	
	SS6.orderStatusDelete.enableSubmit = function($submit) {
		$submit.attr('disabled', false);
		$('#js-order-status-delete-submit-container').removeClass('cursor-help');
		$('#js-order-status-delete-submit-container').tooltip('destroy');
	};
	
	SS6.orderStatusDelete.disableSubmit = function($submit) {
		$submit.attr('disabled', true);
		$('#js-order-status-delete-submit-container').addClass('cursor-help');
		$('#js-order-status-delete-submit-container').tooltip({
			title: 'Nejprve vybrete nový stav'
		});
	};
	
	$(document).ready(function () {
		SS6.orderStatusDelete.init();
	});
	
})(jQuery);
