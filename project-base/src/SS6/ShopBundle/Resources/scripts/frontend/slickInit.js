(function ($) {

	SS6 = window.SS6 || {};
	SS6.slickInit = SS6.slickInit || {};

	SS6.slickInit.init = function () {
		$('#slider-hp').slick({
			dots: true,
			arrows: false,
			autoplay: true,
			autoplaySpeed: 4000
		});
	};

	$(document).ready(function () {
		SS6.slickInit.init();
	});

})(jQuery);
