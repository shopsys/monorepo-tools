(function ($) {

    Shopsys = window.Shopsys || {};

    Shopsys.register.registerCallback(function ($container) {
        $container.filterAllNodes('input[type=file]').bootstrapFileInput();
        $container.filterAllNodes('.file-inputs').bootstrapFileInput();
    });

})(jQuery);
