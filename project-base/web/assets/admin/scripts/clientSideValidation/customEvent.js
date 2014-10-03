(function ($) {
	
	SS6 = window.SS6 || {};
	SS6.clientSideValidation = SS6.clientSideValidation || {};
	
	FpJsFormValidator.customizeMethods._submitForm = FpJsFormValidator.customizeMethods.submitForm;
	FpJsFormValidator.customizeMethods.submitForm = function (event) {
		FpJsFormValidator.customizeMethods._submitForm.call(this, event);
		if ($(this).find('.js-validation-error:first').size() > 0) {
			event.preventDefault();
			alert('Překontrolujte prosím zadané hodnoty');
		}
	}
	
	SS6.clientSideValidation.init = function () {
		$('form :input').each(SS6.clientSideValidation.inputBind);
	};
	
	SS6.clientSideValidation.inputBind = function () {
		$(this)
			.bind('blur change', function () {
				$(this).jsFormValidator('validate')
			})
			.focus(function () {
				$(this).closest('.js-validation-error').removeClass('js-validation-error');
			})
			.jsFormValidator({
				'showErrors': SS6.clientSideValidation.showErrors
			});
	};
	
	SS6.clientSideValidation.showErrors = function (errors) {
		var $formLine = $(this).closest('.form-line');
		var $errorList = $formLine.find('.js-validation-errors-list');
		var $errorListUl = $errorList.find('ul:first');
		if (errors.length > 0) {
			$formLine.addClass('js-validation-error');
			$errorListUl.html('');
			$.each(errors, function (key, message) {
				$errorListUl.append($('<li/>').text(message));
			});
			$errorList.show();
		} else {
			$formLine.removeClass('js-validation-error');
			$errorList.hide();
		}
	};
	
	$(document).ready(function () {
		SS6.clientSideValidation.init();
	});
})(jQuery);