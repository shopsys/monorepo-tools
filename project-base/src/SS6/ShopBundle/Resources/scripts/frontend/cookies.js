(function ($) {

	SS6 = window.SS6 || {};
	var cookieName = SS6.constant('\\SS6\\ShopBundle\\Model\\Cookies\\CookiesFacade::EU_COOKIES_COOKIE_CONSENT_NAME');
	var tenYears = 10 * 365;

	$(document).ready(function () {
		$('.js-eu-cookies-consent-button').click(function () {
			var $cookiesFooterGap = $('.js-eu-cookies-consent-footer-gap');
			var $cookiesBlock = $('.js-eu-cookies');
			$.cookie(cookieName, true, { expires: tenYears, path: '/' });

			$cookiesBlock.addClass('box-cookies--closing');
			$cookiesFooterGap.removeClass('web__footer--with-cookies');
		});
	});

})(jQuery);
