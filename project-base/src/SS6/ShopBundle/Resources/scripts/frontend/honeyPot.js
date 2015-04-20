(function ($) {

	SS6 = window.SS6 || {};
	SS6.honeyPot = SS6.honeyPot || {};

	SS6.honeyPot.init = function () {
		$('.js-honey').hide();
	};

	$(document).ready(function () {
		SS6.honeyPot.init();
	});

})(jQuery);
