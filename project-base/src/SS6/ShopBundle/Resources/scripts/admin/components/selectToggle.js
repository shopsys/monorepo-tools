(function ($) {

	SS6 = window.SS6 || {};
	SS6.selectToggle = SS6.selectToggle || {};

	var optionClassPrefix = 'js-select-toggle-option-';

	SS6.selectToggle.toggleOptgroupOnControlChange = function($select, $control) {
		SS6.selectToggle.setOptgroupClassByLabel($select, optionClassPrefix);

		$control
			.bind('change.selectToggle', function () {
				SS6.selectToggle.showOptionsBySelector($select, '.' + optionClassPrefix + $control.val());
			})
			.trigger('change.selectToggle');
	};

	SS6.selectToggle.showOptionsBySelector = function ($select, optionSelector) {
		$select.find('option').each(function () {
			if ($(this).is(optionSelector)) {
				SS6.toggleOption.show($(this));
			} else {
				SS6.toggleOption.hide($(this));
				$(this).removeAttr('selected')
			}
		});
	};

	SS6.selectToggle.setOptgroupClassByLabel = function ($select, classPrefix) {
		$select.find('optgroup').each(function() {
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