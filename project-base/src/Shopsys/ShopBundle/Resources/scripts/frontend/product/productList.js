(function ($) {

    Shopsys = window.Shopsys || {};
    Shopsys.productList = Shopsys.productList || {};

    Shopsys.register.registerCallback(function () {
        $('.js-product-list-ordering-mode').click(function () {
            var cookieName = $(this).data('cookie-name');
            var orderingName = $(this).data('ordering-mode');

            $.cookie(cookieName, orderingName, { path: '/' });
            location.reload(true);

            return false;
        });
    });

    $(document).ready(function () {
        var ajaxMoreLoader = new Shopsys.productList.AjaxMoreLoader();
        ajaxMoreLoader.init();
        var ajaxFilter = new Shopsys.productList.AjaxFilter(ajaxMoreLoader);
        ajaxFilter.init();
    });

})(jQuery);
