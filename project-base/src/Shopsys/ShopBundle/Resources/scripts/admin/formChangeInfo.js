(function ($) {

    Shopsys = window.Shopsys || {};
    Shopsys.formChangeInfo = Shopsys.formChangeInfo || {};

    var isFormSubmitted = false;
    var isInfoShown = false;

    Shopsys.formChangeInfo.initContent = function ($container) {
        $container.filterAllNodes('.web__content form')
            .change(Shopsys.formChangeInfo.showInfo)
            .each(function() {
                if ($(this).find('.form-input-error:first, .js-validation-errors-list li:first').length > 0) {
                    Shopsys.formChangeInfo.showInfo();
                }
            });
    };

    Shopsys.formChangeInfo.initDocument = function () {
        $(document).on('submit', '.web__content form', function (event) {
            if (event.isDefaultPrevented() === false) {
                isFormSubmitted = true;
            }
        });

        $(window).on('beforeunload', function() {
            if (isInfoShown && !isFormSubmitted) {
                return Shopsys.translator.trans('You have unsaved changes!');
            }
        });
    };

    Shopsys.formChangeInfo.initWysiwygEditors = function () {
        if (typeof CKEDITOR !== 'undefined') {
            for (var i in CKEDITOR.instances) {
                var instance = CKEDITOR.instances[i];
                if (!instance.formChangeInfoInitilized) {
                    instance.on('change', Shopsys.formChangeInfo.showInfo);
                    instance.formChangeInfoInitilized = true;
                }
            }
        }
    };

    Shopsys.formChangeInfo.showInfo = function () {
        var textToShow = Shopsys.translator.trans('You have made changes, don\'t forget to save them!');
        var $fixedBarIn = $('.web__content .window-fixed-bar .window-fixed-bar__in');
        var $infoDiv = $fixedBarIn.find('#js-form-change-info');
        if (!isInfoShown) {
            $fixedBarIn.prepend(
                '<div class="window-fixed-bar__item">\
                    <div id="js-form-change-info" class="window-fixed-bar__item__cell text-center">\
                        <strong>' + textToShow + '</strong>\
                    </div>\
                </div>');
        } else {
            $infoDiv.text = textToShow;
        }
        if ($fixedBarIn.length > 0) {
            isInfoShown = true;
        }
    };

    Shopsys.formChangeInfo.removeInfo = function () {
        $('#js-form-change-info').remove();
        isInfoShown = false;
    };

    Shopsys.register.registerCallback(function ($container) {
        Shopsys.formChangeInfo.initContent($container);
        Shopsys.formChangeInfo.initWysiwygEditors();
    });

    $(document).ready(function () {
        Shopsys.formChangeInfo.initDocument();
    });

})(jQuery);
