(function ($){

    Shopsys = window.Shopsys || {};
    Shopsys.flashMessage = Shopsys.flashMessage || {};

    Shopsys.flashMessage.init = function ($container) {
        $container.filterAllNodes('.js-flash-message .js-flash-message-close')
            .bind('click.closeFlashMessage', Shopsys.flashMessage.onCloseFlashMessage);
    };

    Shopsys.flashMessage.onCloseFlashMessage = function (event) {
        $(this).closest('.js-flash-message').slideUp('fast', function () {
            $(this).remove();
        });
        event.preventDefault();
    };

    Shopsys.register.registerCallback(Shopsys.flashMessage.init);

})(jQuery);
