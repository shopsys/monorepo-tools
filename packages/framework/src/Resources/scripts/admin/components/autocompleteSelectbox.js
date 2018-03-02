(function ($) {

    Shopsys = window.Shopsys || {};

    var autocompleteSelectbox = function ($container) {
        $container.filterAllNodes('select.js-autocomplete-selectbox').selectize();
    };

    Shopsys.register.registerCallback(autocompleteSelectbox);

})(jQuery);
