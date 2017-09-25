(function ($) {

    Shopsys = window.Shopsys || {};
    Shopsys.login = Shopsys.login || {};

    Shopsys.login.init = function () {
        $('body').on('submit', '.js-front-login-window', function () {
            $('.js-front-login-window-message').empty();
            Shopsys.ajax({
                loaderElement: '.js-front-login-window',
                type: 'POST',
                url: $(this).attr('action'),
                data: $(this).serialize(),
                success: function (data) {
                    if (data.success === true) {
                        var $loaderOverlay = Shopsys.loaderOverlay.createLoaderOverlay('.js-front-login-window');
                        Shopsys.loaderOverlay.showLoaderOverlay($loaderOverlay);

                        document.location = data.urlToRedirect;
                    } else {
                        $('.js-front-login-window-message')
                            .text(Shopsys.translator.trans('Invalid login'))
                            .show();
                    }
                }
            });
            return false;
        });
        $('body').on('focus', '.js-front-login-window', function () {
            $('.js-front-login-window-message').empty().hide();
        });
    };

    $(document).ready(function () {
        Shopsys.login.init();
    });

})(jQuery);
