(function ($) {

    Shopsys = window.Shopsys || {};
    Shopsys.login = Shopsys.login || {};

    Shopsys.login.Login = function () {

        this.init = function ($loginButton) {
            $loginButton.click(showWindow);
        };

        function showWindow (event) {
            Shopsys.ajax({
                url: $(this).data('url'),
                type: 'POST',
                success: function (data) {
                    var $window = Shopsys.window({
                        content: data,
                        textHeading: Shopsys.translator.trans('Login')
                    });

                    $window.on('submit', '.js-front-login-window', onSubmit);
                }
            });

            event.preventDefault();
        }

        function onSubmit () {
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
                        var $validationErrors = $('.js-window-validation-errors');
                        if ($validationErrors.hasClass('display-none')) {
                            $validationErrors
                                .text(Shopsys.translator.trans('This account doesn\'t exist or password is incorrect'))
                                .show();
                        }

                    }
                }
            });
            return false;
        }

    };

    Shopsys.register.registerCallback(function ($container) {
        $container.filterAllNodes('.js-login-button').each(function () {
            var $login = new Shopsys.login.Login();
            $login.init($(this));
        });
    });

})(jQuery);
