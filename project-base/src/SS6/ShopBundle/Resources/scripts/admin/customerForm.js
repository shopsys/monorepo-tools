(function ($) {

	SS6.register.registerCallback(function ($container) {
		var $select = $container.filterAllNodes('#customer_form_userData_pricingGroup');
		var $control = $container.filterAllNodes('#customer_form_userData_domainId');

		if ($select.length > 0 && $control.length > 0) {
			SS6.selectToggle.toggleOptgroupOnControlChange($select, $control);
		}
	});

})(jQuery);
