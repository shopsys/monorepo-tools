(function ($) {

    Shopsys = window.Shopsys || {};
    Shopsys.productImagesSort = Shopsys.productImagesSort || {};

    Shopsys.productImagesSort.init = function () {
        $('.js-image-upload').sortable({
            handle: '.js-image-upload-image-handle',
            update: Shopsys.formChangeInfo.showInfo
        });
    };

    $(document).ready(function () {
        Shopsys.productImagesSort.init();
    });

})(jQuery);
