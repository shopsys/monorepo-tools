(function ($) {

	SS6 = window.SS6 || {};
	SS6.confirmDelete = SS6.confirmDelete || {};
	
	SS6.confirmDelete.init = function () {
		$(document).on('change', '#js-confirm-delete-select', SS6.confirmDelete.onSelectChange);
		$(document).on('submit', '#js-confirm-delete-form', SS6.confirmDelete.onFormSubmit);
	};
	
	SS6.confirmDelete.onSelectChange = function () {
		var $submitButton = $('#js-confirm-delete-form').find('.btn-primary');
		if ($('#js-confirm-delete-select').val() !== '') {
			$submitButton
				.removeClass('btn--alter cursor-help')
				.tooltip('destroy');
		} else {
			$submitButton
				.addClass('btn--alter cursor-help')
				.tooltip({
					title: 'Nejprve vybrete novou hodnotu',
					placement: 'right'
				});
		}
	};
	
	SS6.confirmDelete.onFormSubmit = function() {
		var targetUrl = $('#js-confirm-delete-select').val();
		if (targetUrl !== '') {
			document.location = targetUrl;
		}
		
		return false;
	};
	
	$(document).ready(function () {
		SS6.confirmDelete.init();
	});
	
})(jQuery);
