(function ($) {

	SS6 = window.SS6 || {};
	SS6.login = SS6.login || {};

	SS6.login.init = function () {
		$('body').on('submit', '.js-front-login-window', function() {
			$('.js-front-login-window-message').empty();
			$.ajax({
				type: 'POST',
				url: $(this).attr('action'),
				data: $(this).serialize(),
				success: function (data) {
					if (data.success === true) {
						document.location = data.urlToRedirect;
					} else {
						$('.js-front-login-window-message').text(SS6.translator.trans('Byly zadány neplatné přihlašovací údaje'));
					}
				}
			});
			return false;
		});
		$('body').on('focus', '.js-front-login-window', function() {
			$('.js-front-login-window-message').empty();
		});
	};

	$(document).ready(function () {
		SS6.login.init();
	});

})(jQuery);
