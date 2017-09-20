(function ($) {

    Shopsys = window.Shopsys || {};
    Shopsys.dynamicPlaceholder = Shopsys.dynamicPlaceholder || {};

    Shopsys.dynamicPlaceholder.DynamicPlaceholder = function ($input) {
        var $sourceInput = $('#' + $input.data('placeholder-source-input-id'));

        this.init = function() {
            $sourceInput.change(function () {
                updatePlaceholder();
            });

            updatePlaceholder();
        };

        var updatePlaceholder = function () {
            $input.attr('placeholder', $sourceInput.val());
            $input.trigger('placeholderChange');
        };
    };

    Shopsys.register.registerCallback(function ($container) {
        $container.filterAllNodes('.js-dynamic-placeholder').each(function () {
            var dynamicPlaceholder = new Shopsys.dynamicPlaceholder.DynamicPlaceholder($(this));
            dynamicPlaceholder.init();
        });
    });

})(jQuery);
