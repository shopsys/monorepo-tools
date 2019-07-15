(function ($) {

    Shopsys = window.Shopsys || {};
    Shopsys.product = Shopsys.product || {};

    Shopsys.product.init = function () {
        var usingStockSelection = $('#product_form_displayAvailabilityGroup_usingStock input[type="radio"]');
        var $outOfStockActionSelection = $('select[name="product_form[displayAvailabilityGroup][stockGroup][outOfStockAction]"]');

        usingStockSelection.change(function () {
            Shopsys.product.toggleIsUsingStock($(this).val() === '1');
        });

        $outOfStockActionSelection.change(function () {
            Shopsys.product.toggleIsUsingAlternateAvailability($(this).val() === Shopsys.constant('\\Shopsys\\FrameworkBundle\\Model\\Product\\Product::OUT_OF_STOCK_ACTION_SET_ALTERNATE_AVAILABILITY'));
        });

        Shopsys.product.toggleIsUsingStock(usingStockSelection.filter(':checked').val() === '1');
        Shopsys.product.toggleIsUsingAlternateAvailability($outOfStockActionSelection.val() === Shopsys.constant('\\Shopsys\\FrameworkBundle\\Model\\Product\\Product::OUT_OF_STOCK_ACTION_SET_ALTERNATE_AVAILABILITY'));

        Shopsys.product.initializeSideNavigation();
    };

    Shopsys.product.toggleIsUsingStock = function (isUsingStock) {
        $('.js-product-using-stock').toggle(isUsingStock);
        $('.js-product-not-using-stock').closest('.form-line').toggle(!isUsingStock);
    };

    Shopsys.product.toggleIsUsingAlternateAvailability = function (isUsingStockAndAlternateAvailability) {
        $('.js-product-using-stock-and-alternate-availability').closest('.form-line').toggle(isUsingStockAndAlternateAvailability);
    };

    Shopsys.product.initializeSideNavigation = function () {
        var $productDetailNavigation = $('.js-product-detail-navigation');
        var $webContent = $('.web__content');

        $('.form-group__title, .form-full__title').each(function () {
            var $title = $(this);
            var $titleClone = $title.clone();

            $titleClone.find('.js-validation-errors-list').remove();
            var $navigationItem = $('<li class="side-menu__item"><span class="side-menu__item__link"><span class="side-menu__item__text">' + $titleClone.text() + '</span></span></li>');
            $productDetailNavigation.append($navigationItem);

            $navigationItem.click(function () {
                var scrollOffsetTop = $title.offset().top - $webContent.offset().top;
                $('html, body').animate({ scrollTop: scrollOffsetTop }, 'slow');
            });
        });
    };

    $(document).ready(function () {
        Shopsys.product.init();
    });

})(jQuery);
