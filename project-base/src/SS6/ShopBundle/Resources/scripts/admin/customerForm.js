(function ($) {

	$(document).ready(function() {
		SS6.selectToggle.toggleOptgroupOnControlChange(
			$('#customer_form_userData_pricingGroup'),
			$('#customer_form_userData_domainId')
		);
	});

})(jQuery);
