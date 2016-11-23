(function ($) {

	SS6 = window.SS6 || {};
	SS6.login = SS6.login || {};

	SS6.login.init = function () {
		$('body').on('submit', '.js-front-login-window', function() {
			$('.js-front-login-window-message').empty();
			SS6.ajax({
				loaderElement: '.js-front-login-window',
				type: 'POST',
				url: $(this).attr('action'),
				data: $(this).serialize(),
				success: function (data) {
					if (data.success === true) {
						var $loaderOverlay = SS6.loaderOverlay.createLoaderOverlay('.js-front-login-window');
						SS6.loaderOverlay.showLoaderOverlay($loaderOverlay);

						document.location = data.urlToRedirect;
					} else {
						$('.js-front-login-window-message')
							.text(SS6.translator.trans('Byly zadány neplatné přihlašovací údaje'))
							.show();
					}
				}
			});
			return false;
		});
		$('body').on('focus', '.js-front-login-window', function() {
			$('.js-front-login-window-message').empty().hide();
		});
	};

	$(document).ready(function () {
		SS6.login.init();
	});

})(jQuery);
