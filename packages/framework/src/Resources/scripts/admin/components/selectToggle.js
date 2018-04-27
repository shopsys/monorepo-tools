(function ($) {

    Shopsys = window.Shopsys || {};
    Shopsys.selectToggle = Shopsys.selectToggle || {};

    var optionClassPrefix = 'js-select-toggle-option-';

    Shopsys.register.registerCallback(function ($container) {
        var $selects = $container.filterAllNodes('.js-toggle-opt-group');

        if ($selects.length > 0) {
            $selects.each(function () {
                Shopsys.selectToggle.toggleOptgroupOnControlChange($(this));
            });
        }
    });

    Shopsys.selectToggle.toggleOptgroupOnControlChange = function ($select) {
        Shopsys.selectToggle.setOptgroupClassByLabel($select, optionClassPrefix);

        var $control = $($select.data('js-toggle-opt-group-control'));

        if ($control.length > 0) {
            $control
                .bind('change.selectToggle', function () {
                    Shopsys.selectToggle.showOptionsBySelector($select, '.' + optionClassPrefix + $control.val());
                })
                .trigger('change.selectToggle');
        }
    };

    Shopsys.selectToggle.showOptionsBySelector = function ($select, optionSelector) {
        $select.find('option').each(function () {
            if ($(this).is(optionSelector)) {
                Shopsys.toggleOption.show($(this));
            } else {
                Shopsys.toggleOption.hide($(this));
                $(this).removeAttr('selected');
            }
        });
    };

    Shopsys.selectToggle.setOptgroupClassByLabel = function ($select, classPrefix) {
        $select.find('optgroup').each(function () {
            var $optgroup = $(this);
            var optgroupLabel = $optgroup.attr('label');
            $optgroup.find('option').each(function () {
                $(this)
                    .addClass(classPrefix + optgroupLabel)
                    .appendTo($select);
            });

            $optgroup.remove();
        });
    };

})(jQuery);
