(function ($){
	$.fn.SS6 = $.fn.SS6 || {};
	$.fn.SS6.flashMessage = $.fn.SS6.addProduct || {};
	
	$.fn.SS6.flashMessage.init = function (formElement) {
		$('.js-flash-message .js-flash-message-close').bind('click.closeFlashMessage', $.fn.SS6.flashMessage.onCloseFlashMessage);
	}
	
	$.fn.SS6.flashMessage.onCloseFlashMessage = function (event) {
		$(this).closest('.js-flash-message').slideUp('fast', function () {
			$(this).remove();
		});
		event.preventDefault();
	}
	
	
	$(document).ready(function () {
		$.fn.SS6.flashMessage.init();
	});
	
})(jQuery);