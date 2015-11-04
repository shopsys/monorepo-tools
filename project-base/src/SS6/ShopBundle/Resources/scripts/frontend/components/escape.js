(function ($) {

	SS6 = window.SS6 || {};
	SS6.escape = SS6.escape || {};

	SS6.escape.escapeHtml = function (string) {
		return $("<textarea/>").text(string).html();
	};

})(jQuery);
