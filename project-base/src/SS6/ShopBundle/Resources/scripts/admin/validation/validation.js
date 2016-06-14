(function ($) {

	SS6 = window.SS6 || {};
	SS6.validation = SS6.validation || {};

	$(document).ready(function () {
		$('.js-no-validate-button').click(function () {
			$(this).closest('form').addClass('js-no-validate');
		});
	});

	SS6.validation.forceValidateElement = function ($element) {
		$element.jsFormValidator('validate');

		if ($element.jsFormValidator) {
			var parent = $element.jsFormValidator.parent;
			while (parent) {
				parent.validate();

				parent = parent.parent;
			}
		}
	};

	SS6.validation.findElementsToHighlight = function ($formInput) {
		return $formInput.filter('input, select, textarea, .form-line, .table-form');
	};

	SS6.validation.highlightSubmitButtons = function($form){
		var $submitButtons = $form.find('.btn[type="submit"]');

		if (SS6.validation.isFormValid($form)) {
			$submitButtons.removeClass('btn--disabled');
		} else {
			$submitButtons.addClass('btn--disabled');
		}
	};

	$(document).ready(function () {
		var $formattedFormErrors = SS6.validation.getFormattedFormErrors(document);
		$('.js-flash-message.in-message--danger').append($formattedFormErrors);
	});

})(jQuery);
