(function ($) {

	Shopsys = window.Shopsys || {};
	Shopsys.validation = Shopsys.validation || {};

	$(document).ready(function () {
		$('.js-no-validate-button').click(function () {
			$(this).closest('form').addClass('js-no-validate');
		});
		$('.js-validation-error-close').click(function () {
			$(this).closest('.js-validation-error').hide();
		});
		$('.js-validation-error-toggle').click(function () {
			$(this)
				.closest('.js-validation-errors-list')
				.find('.js-validation-error')
				.toggle();
		});
	});

	Shopsys.validation.findElementsToHighlight = function ($formInput) {
		return $formInput.filter('input, select, textarea, .form-line');
	};

	Shopsys.validation.highlightSubmitButtons = function($form){
		var $submitButtons = $form.find('.btn[type="submit"]:not(.js-no-validate-button)');

		if (Shopsys.validation.isFormValid($form)) {
			$submitButtons.removeClass('btn--disabled');
		} else {
			$submitButtons.addClass('btn--disabled');
		}
	};

})(jQuery);
