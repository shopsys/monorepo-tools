(function ($) {

	$(document).ready(function () {
		var $embedOnlyInOrderSentPageCheckbox = $('input[name="script_form[placement]"]');

		var toggleScriptVariables = function() {
			var isChecked = $embedOnlyInOrderSentPageCheckbox.prop('checked');
			$('#js-order-sent-page-variables').toggle(isChecked);
		};

		toggleScriptVariables();
		$embedOnlyInOrderSentPageCheckbox.on('change', function() {
			toggleScriptVariables();
		});
	});

})(jQuery);
