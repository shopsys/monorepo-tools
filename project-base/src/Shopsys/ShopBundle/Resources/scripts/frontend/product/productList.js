(function ($) {

    Shopsys = window.Shopsys || {};
    Shopsys.productList = Shopsys.productList || {};

    Shopsys.register.registerCallback(function ($container) {
        $container.filterAllNodes('.js-product-list-ordering-mode').click(function () {
            var cookieName = $(this).data('cookie-name');
            var orderingName = $(this).data('ordering-mode');

            $.cookie(cookieName, orderingName, { path: '/' });
            location.reload(true);

            return false;
        });

        $container.filterAllNodes('.js-product-list-with-paginator').each(function () {
            var ajaxMoreLoader = new Shopsys.AjaxMoreLoader($(this), {
                buttonTextCallback: function (loadNextCount) {
                    return Shopsys.translator.transChoice(
                        '{1}Load next %loadNextCount% product|[2,Inf]Load next %loadNextCount% products',
                        loadNextCount,
                        { '%loadNextCount%': loadNextCount }
                    );
                }
            });
            ajaxMoreLoader.init();
        });
    });

    $(document).ready(function () {
        $('.js-product-list-with-paginator').each(function () {
            var ajaxFilter = new Shopsys.productList.AjaxFilter();
            ajaxFilter.init();
        });
    });

})(jQuery);
