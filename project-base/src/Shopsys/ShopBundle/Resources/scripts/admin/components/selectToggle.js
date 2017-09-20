(function ($) {

    Shopsys = window.Shopsys || {};
    Shopsys.selectToggle = Shopsys.selectToggle || {};

    var optionClassPrefix = 'js-select-toggle-option-';

    Shopsys.selectToggle.toggleOptgroupOnControlChange = function ($select, $control) {
        Shopsys.selectToggle.setOptgroupClassByLabel($select, optionClassPrefix);

        $control
            .bind('change.selectToggle', function () {
                Shopsys.selectToggle.showOptionsBySelector($select, '.' + optionClassPrefix + $control.val());
            })
            .trigger('change.selectToggle');
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