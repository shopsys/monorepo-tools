(function ($) {

	SS6 = window.SS6 || {};
	SS6.responsive = SS6.responsive || {};
	SS6.productDetail = SS6.productDetail || {};

	SS6.productDetail.init = function () {
		var $gallery = $('.js-gallery');

		$gallery.magnificPopup({
			type: 'image',
			delegate: 'a',
			gallery: {
				enabled: true,
				navigateByImgClick: true,
				preload: [0,1]
			}
		});

		$gallery.filterAllNodes('.js-gallery-slides').slick({
			dots: false,
			arrows: true,
			slidesToShow: 2,
			slidesToScroll: 1,
			lazyLoad: 'ondemand',
			mobileFirst: true,
			prevArrow: $gallery.filterAllNodes('.js-gallery-prev'),
			nextArrow: $gallery.filterAllNodes('.js-gallery-next'),
			responsive: [
				{
					breakpoint: SS6.responsive.XS,
					settings: {
						slidesToShow: 3,
						slidesToScroll: 2
					}
				},
				{
					breakpoint: SS6.responsive.MD,
					settings: {
						slidesToShow: 4,
						slidesToScroll: 3
					}
				},
				{
					breakpoint: SS6.responsive.LG,
					settings: {
						slidesToShow: 3,
						slidesToScroll: 2
					}
				},
				{
					breakpoint: SS6.responsive.VL,
					settings: {
						slidesToShow: 4,
						slidesToScroll: 3
					}
				}
			]
		});
	};

	$(document).ready(function () {
		SS6.productDetail.init();
	});

})(jQuery);
