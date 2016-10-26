(function ($) {

	SS6 = window.SS6 || {};
	SS6.form = SS6.form || {};

	SS6.form.disableDoubleSubmit = function ($container) {
		$container.filterAllNodes('form').each(function () {
			var isFormSubmittingDisabled = false;

			$(this).submit(function (event) {
				if (isFormSubmittingDisabled) {
					event.stopImmediatePropagation();
					event.preventDefault();
				} else {
					isFormSubmittingDisabled = true;
					setTimeout(function () {
						isFormSubmittingDisabled = false;
					}, 200);
				}
			});
		});
	};

	SS6.register.registerCallback(SS6.form.disableDoubleSubmit, SS6.register.CALL_PRIORITY_HIGH);

})(jQuery);