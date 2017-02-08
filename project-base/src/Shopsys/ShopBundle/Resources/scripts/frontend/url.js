(function ($) {

	SS6 = window.SS6 || {};
	SS6.url = SS6.url || {};

	SS6.url.getBaseUrl = function () {
		return document.location.protocol
			+ '//'
			+ document.location.host
			+ document.location.pathname;
	};

})(jQuery);
