(function ($) {

	Shopsys.register.registerCallback(function ($container) {
		var $embedOnlyInOrderSentPageCheckbox = $container.filterAllNodes('input[name="script_form[placement]"]');

		if ($embedOnlyInOrderSentPageCheckbox.length > 0) {
			var toggleScriptVariables = function () {
				var isChecked = $embedOnlyInOrderSentPageCheckbox.prop('checked');
				$('#js-order-sent-page-variables').toggle(isChecked);
			};

			toggleScriptVariables();
			$embedOnlyInOrderSentPageCheckbox.on('change', function () {
				toggleScriptVariables();
			});
		}
	});

})(jQuery);
