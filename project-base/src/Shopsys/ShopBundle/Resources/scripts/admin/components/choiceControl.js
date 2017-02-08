(function ($) {

	SS6 = window.SS6 || {};
	SS6.choiceControl = SS6.choiceControl || {};

	SS6.choiceControl.select = function (choiceListSelector, value) {
		var $choiceList = $($(choiceListSelector));

		var choice = SS6.choiceControl.findChoice($choiceList, value);
		var $choice = $(choice);
		if ($choice.is('input')) {
			$choice.prop('checked', true);
		} else if ($choice.is('option')) {
			$choice.prop('selected', true);
		}
	};

	SS6.choiceControl.deselect = function (choiceListSelector, value) {
		var $choiceList = $($(choiceListSelector));

		var choice = SS6.choiceControl.findChoice($choiceList, value);
		var $choice = $(choice);
		if ($choice.is('input')) {
			$choice.prop('checked', false);
		} else if ($choice.is('option')) {
			$choice.prop('selected', false);
		}
	};

	SS6.choiceControl.deselectAll = function (choiceListSelector) {
		var $choiceList = $($(choiceListSelector));

		SS6.choiceControl.findAllChoices($choiceList).each(function (key, element) {
			var $choice = $(element);
			if ($choice.is('input')) {
				$choice.prop('checked', false);
			} else if ($choice.is('option')) {
				$choice.prop('selected', false);
			}
		});
	};

	SS6.choiceControl.getSelectedValue = function (choiceListSelector) {
		var values = SS6.choiceControl.getSelectedValues(choiceListSelector);

		return (values[0] !== undefined) ? values[0] : null;
	};

	SS6.choiceControl.getSelectedValues = function (choiceListSelector) {
		var $choiceList = $(choiceListSelector);

		var values = [];

		SS6.choiceControl.findAllChoices($choiceList).each(function (key, element) {
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

	SS6.choiceControl.findChoice = function ($choiceList, value) {
		return SS6.choiceControl.findAllChoices($choiceList).filter(function (key, element) {
			var $element = $(element);
			return parseInt($element.val()) === value;
		});
	};

	SS6.choiceControl.findAllChoices = function ($choiceList) {
		return $choiceList.find('input, option');
	};

	SS6.choiceControl.getNewIndex = function ($choiceList) {
		var maxIndex = 0;
		SS6.choiceControl.findAllChoices($choiceList).each(function (key, element) {
			var $input = $(element);
			var index = parseInt($input.attr('name').replace(/.*\[(.+)\]/, '$1'));
			if (index > maxIndex) {
				maxIndex = index;
			}
		});

		return maxIndex + 1;
	};

})(jQuery);
