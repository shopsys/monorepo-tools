(function ($) {

    Shopsys = window.Shopsys || {};
    Shopsys.ajaxConfirm = Shopsys.ajaxConfirm || {};

    Shopsys.ajaxConfirm.init = function ($container) {
        $container.filterAllNodes('a.js-ajax-confirm').each(Shopsys.ajaxConfirm.bind);
    };

    Shopsys.ajaxConfirm.bind = function () {
        $(this)
            .unbind('click.ajaxConfirm')
            .bind('click.ajaxConfirm', function () {
                Shopsys.ajax({
                    url: $(this).attr('href'),
                    context: this,
                    success: function(data) {
                        Shopsys.window({
                            content: data
                        });
                        var onOpen = $(this).data('ajax-confirm-on-open');
                        if (onOpen) {
                            eval(onOpen);
                        }
                    }
                });

                return false;
            });
    };

    Shopsys.register.registerCallback(Shopsys.ajaxConfirm.init);

})(jQuery);
