(function ($) {

    Shopsys = window.Shopsys || {};
    Shopsys.responsive = Shopsys.responsive || {};
    Shopsys.productDetail = Shopsys.productDetail || {};

    Shopsys.productDetail.init = function () {
        $('.js-gallery-main-image').click(function (event) {
            var $slides = $('.js-gallery .slick-slide:not(.slick-cloned) .js-gallery-slide-link');
            $slides.filter(':first').trigger('click', event);

            return false;
        });

        var $gallery = $('.js-gallery');

        $gallery.magnificPopup({
            type: 'image',
            delegate: '.js-gallery-slide-link',
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
            infinite: false,
            prevArrow: $gallery.filterAllNodes('.js-gallery-prev'),
            nextArrow: $gallery.filterAllNodes('.js-gallery-next'),
            responsive: [
                {
                    breakpoint: Shopsys.responsive.XS,
                    settings: {
                        slidesToShow: 3,
                        slidesToScroll: 2
                    }
                },
                {
                    breakpoint: Shopsys.responsive.MD,
                    settings: {
                        slidesToShow: 4,
                        slidesToScroll: 3
                    }
                },
                {
                    breakpoint: Shopsys.responsive.LG,
                    settings: {
                        slidesToShow: 3,
                        slidesToScroll: 2
                    }
                },
                {
                    breakpoint: Shopsys.responsive.VL,
                    settings: {
                        slidesToShow: 4,
                        slidesToScroll: 3
                    }
                }
            ]
        });
    };

    $(document).ready(function () {
        Shopsys.productDetail.init();
    });

})(jQuery);
