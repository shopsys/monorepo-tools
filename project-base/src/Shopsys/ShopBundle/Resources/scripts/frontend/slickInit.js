(function ($) {

	Shopsys = window.Shopsys || {};
	Shopsys.slickInit = Shopsys.slickInit || {};

	Shopsys.slickInit.init = function () {
		$('#js-slider-homepage').slick({
			dots: true,
			arrows: false,
			autoplay: true,
			autoplaySpeed: 4000
		});
	};

	$(document).ready(function () {
		Shopsys.slickInit.init();
	});

})(jQuery);