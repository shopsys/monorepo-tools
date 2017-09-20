(function ($) {

    Shopsys = window.Shopsys || {};
    Shopsys.charactersCounter = Shopsys.charactersCounter || {};

    Shopsys.charactersCounter.Counter = function ($counter) {
        var $input = $counter.find('.js-characters-counter-input input, input.js-characters-counter-input, textarea.js-characters-counter-input');
        var $info = $counter.find('.js-characters-counter-info');
        var recommendedLength = $info.data('recommended-length');

        this.init = function () {
            if ($input.length > 0) {
                $input.bind('keyup placeholderChange', countCharacters);
                countCharacters();
            }
        };

        var countCharacters = function () {
            var currentLength = $input.val().length;
            var placeholder = $input.attr('placeholder');
            if (currentLength === 0 && placeholder) {
                currentLength = placeholder.length;
            }
            $info.text(Shopsys.translator.trans(
                'Used: %currentLength% characters. Recommended max. %recommendedLength%',
                {
                    '%currentLength%': currentLength,
                    '%recommendedLength%': recommendedLength
                }
            ));
        };

    };

    Shopsys.register.registerCallback(function ($container) {
        $container.filterAllNodes('.js-characters-counter').each(function () {
            var instance = new Shopsys.charactersCounter.Counter($(this));
            instance.init();
        });
    });

})(jQuery);