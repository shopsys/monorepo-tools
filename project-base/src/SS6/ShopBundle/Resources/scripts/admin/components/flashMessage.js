(function ($){

	SS6 = window.SS6 || {};
	SS6.flashMessage = SS6.flashMessage || {};

	SS6.flashMessage.init = function () {
		$('.js-flash-message .js-flash-message-close').bind('click.closeFlashMessage', SS6.flashMessage.onCloseFlashMessage);
	};

	SS6.flashMessage.onCloseFlashMessage = function (event) {
		$(this).closest('.js-flash-message').slideUp('fast', function () {
			$(this).remove();
		});
		event.preventDefault();
	};


	$(document).ready(function () {
		SS6.flashMessage.init();
	});

})(jQuery);
