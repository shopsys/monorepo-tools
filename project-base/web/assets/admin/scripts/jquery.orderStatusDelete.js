(function ($) {

	$.fn.SS6 = $.fn.SS6 || {};
	$.fn.SS6.orderStatusDelete = $.fn.SS6.orderStatusDelete || {};
	
	$.fn.SS6.orderStatusDelete.init = function () {
		$('.js-order-status-delete-button').click($.fn.SS6.orderStatusDelete.onDelete);
		
		$(document).on('change', '#js-order-status-delete-status-select', function() {
			if ($(this).val() !== '') {
				$.fn.SS6.orderStatusDelete.enableSubmit($('#js-order-status-delete-submit'));
			} else {
				$.fn.SS6.orderStatusDelete.disableSubmit($('#js-order-status-delete-submit'));
			}
		});
	};
	
	$.fn.SS6.orderStatusDelete.onDelete = function(event) {
		$.ajax({
			url: $(this).data('ajax-action'),
			type: 'POST',
			success: $.fn.SS6.orderStatusDelete.onDeleteResponseSuccess
		});
		
		event.preventDefault();
		return false;
	};
	
	$.fn.SS6.orderStatusDelete.onDeleteResponseSuccess = function(data) {
		$('#js-order-status-delete-window').html(data);
		$.fn.SS6.window.open('orderStatusDelete');
		$.fn.SS6.orderStatusDelete.disableSubmit($('#js-order-status-delete-submit'));
	};
	
	$.fn.SS6.orderStatusDelete.enableSubmit = function($submit) {
		$submit.attr('disabled', false);
		$('#js-order-status-delete-submit-container').removeClass('cursor-help');
		$('#js-order-status-delete-submit-container').tooltip('destroy');
	};
	
	$.fn.SS6.orderStatusDelete.disableSubmit = function($submit) {
		$submit.attr('disabled', true);
		$('#js-order-status-delete-submit-container').addClass('cursor-help');
		$('#js-order-status-delete-submit-container').tooltip({
			title: 'Nejprve vybrete nový stav'
		});
	};
	
	$(document).ready(function () {
		$.fn.SS6.orderStatusDelete.init();
	});
	
})(jQuery);
