(function ($) {

	Shopsys = window.Shopsys || {};
	Shopsys.url = Shopsys.url || {};

	Shopsys.url.getBaseUrl = function () {
		return document.location.protocol
			+ '//'
			+ document.location.host
			+ document.location.pathname;
	};

})(jQuery);
