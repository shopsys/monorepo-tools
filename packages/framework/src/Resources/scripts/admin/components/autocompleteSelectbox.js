(function ($) {

    Shopsys = window.Shopsys || {};

    // TODO: till this https://github.com/selectize/selectize.js/issues/1395 issue is not fixed
    Selectize.prototype.positionDropdown = function () {
        var $control = this.$control;
        var offset = this.settings.dropdownParent === 'body' ? $control.offset() : $control.position();
        var bottomOffset = Shopsys.view.getBottomOffset();
        offset.top += $control.outerHeight(true);

        var css = {
            width: $control.outerWidth(),
            top: offset.top,
            left: offset.left,
            bottom: 'auto'
        };

        this.$dropdown.css(css);

        if (this.$dropdown.offset().top + this.$dropdown.height() - $(window).scrollTop() + bottomOffset > $(window).height()) {
            css.bottom = offset.top;
            css.top = 'auto';
            this.$dropdown.css(css);
        }

    };

    (function (oldRefreshOptions) {
        Selectize.prototype.refreshOptions = function (triggerDropdown) {
            oldRefreshOptions.call(this, triggerDropdown);
            this.positionDropdown();
        };
    })(Selectize.prototype.refreshOptions);

    var autocompleteSelectbox = function ($container) {
        $container.filterAllNodes('select.js-autocomplete-selectbox').selectize();
    };

    Shopsys.register.registerCallback(autocompleteSelectbox);

})(jQuery);
