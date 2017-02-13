(function ($) {

    Shopsys = window.Shopsys || {};
    Shopsys.choiceControl = Shopsys.choiceControl || {};

    Shopsys.choiceControl.select = function (choiceListSelector, value) {
        var $choiceList = $($(choiceListSelector));

        var choice = Shopsys.choiceControl.findChoice($choiceList, value);
        var $choice = $(choice);
        if ($choice.is('input')) {
            $choice.prop('checked', true);
        } else if ($choice.is('option')) {
            $choice.prop('selected', true);
        }
    };

    Shopsys.choiceControl.deselect = function (choiceListSelector, value) {
        var $choiceList = $($(choiceListSelector));

        var choice = Shopsys.choiceControl.findChoice($choiceList, value);
        var $choice = $(choice);
        if ($choice.is('input')) {
            $choice.prop('checked', false);
        } else if ($choice.is('option')) {
            $choice.prop('selected', false);
        }
    };

    Shopsys.choiceControl.deselectAll = function (choiceListSelector) {
        var $choiceList = $($(choiceListSelector));

        Shopsys.choiceControl.findAllChoices($choiceList).each(function (key, element) {
            var $choice = $(element);
            if ($choice.is('input')) {
                $choice.prop('checked', false);
            } else if ($choice.is('option')) {
                $choice.prop('selected', false);
            }
        });
    };

    Shopsys.choiceControl.getSelectedValue = function (choiceListSelector) {
        var values = Shopsys.choiceControl.getSelectedValues(choiceListSelector);

        return (values[0] !== undefined) ? values[0] : null;
    };

    Shopsys.choiceControl.getSelectedValues = function (choiceListSelector) {
        var $choiceList = $(choiceListSelector);

        var values = [];

        Shopsys.choiceControl.findAllChoices($choiceList).each(function (key, element) {
            var $element = $(element);
            if ($element.is('input')) {
                if ($element.is(':checked')) {
                    values.push(parseInt($element.val()));
                }
            } else if ($element.is('option')) {
                if ($element.is(':selected')) {
                    values.push(parseInt($element.val()));
                }
            }
        });

        return values;
    };

    Shopsys.choiceControl.findChoice = function ($choiceList, value) {
        return Shopsys.choiceControl.findAllChoices($choiceList).filter(function (key, element) {
            var $element = $(element);
            return parseInt($element.val()) === value;
        });
    };

    Shopsys.choiceControl.findAllChoices = function ($choiceList) {
        return $choiceList.find('input, option');
    };

    Shopsys.choiceControl.getNewIndex = function ($choiceList) {
        var maxIndex = 0;
        Shopsys.choiceControl.findAllChoices($choiceList).each(function (key, element) {
            var $input = $(element);
            var index = parseInt($input.attr('name').replace(/.*\[(.+)\]/, '$1'));
            if (index > maxIndex) {
                maxIndex = index;
            }
        });

        return maxIndex + 1;
    };

})(jQuery);
