/**
 * IE compatible hiding of select's options
 */
(function ($) {

    Shopsys = window.Shopsys || {};
    Shopsys.toggleOption = Shopsys.toggleOption || {};

    var wrapperClass = 'js-toggle-option-wrapper';

    Shopsys.toggleOption.hide = function ($element) {
        $element.hide();
        if ($element.parent('span.' + wrapperClass).length === 0) {
            $element.wrap('<span class="' + wrapperClass + '" style="display: none;" />');
        }
    };

    Shopsys.toggleOption.show = function ($element) {
        $element.show();
        if ($element.parent('span.' + wrapperClass).length > 0) {
            $element.unwrap();
        }
    };
})(jQuery);
