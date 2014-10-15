(function ($) {

	SS6 = window.SS6 || {};
	SS6.slickInit = SS6.slickInit || {};

	SS6.slickInit.init = function () {
		$('.my-carousel').slick({
			dots: true,
			arrows: false,
			autoplay: true,
			autoplaySpeed: 1000,
		});	
	}


	$(document).ready(function () {
		SS6.slickInit.init();
	});

})(jQuery);

