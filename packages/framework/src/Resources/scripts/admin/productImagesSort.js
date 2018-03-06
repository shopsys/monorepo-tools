(function ($) {

    Shopsys = window.Shopsys || {};
    Shopsys.productImagesSort = Shopsys.productImagesSort || {};

    Shopsys.productImagesSort.init = function () {
        $('#js-product-images').sortable({
            handle: '.js-product-images-image-handle',
            update: Shopsys.formChangeInfo.showInfo
        });
    };

    $(document).ready(function () {
        Shopsys.productImagesSort.init();
    });

})(jQuery);
