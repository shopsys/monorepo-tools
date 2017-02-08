(function ($) {

	Shopsys = window.Shopsys || {};
	Shopsys.validation = Shopsys.validation || {};

	$(document).ready(function () {
		$('.js-no-validate-button').click(function () {
			$(this).closest('form').addClass('js-no-validate');
		});
	});

	Shopsys.validation.forceValidateElement = function ($element) {
		$element.jsFormValidator('validate');

		if ($element.jsFormValidator) {
			var parent = $element.jsFormValidator.parent;
			while (parent) {
				parent.validate();

				parent = parent.parent;
			}
		}
	};

	Shopsys.validation.findElementsToHighlight = function ($formInput) {
		return $formInput.filter('input, select, textarea, .form-line, .table-form');
	};

	Shopsys.validation.highlightSubmitButtons = function($form){
		var $submitButtons = $form.find('.btn[type="submit"]');

		if (Shopsys.validation.isFormValid($form)) {
			$submitButtons.removeClass('btn--disabled');
		} else {
			$submitButtons.addClass('btn--disabled');
		}
	};

	$(document).ready(function () {
		var $formattedFormErrors = Shopsys.validation.getFormattedFormErrors(document);
		$('.js-flash-message.in-message--danger').append($formattedFormErrors);
	});

})(jQuery);
