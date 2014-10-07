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
	
	// stop error bubbling
	FpJsFormValidator._getErrorPathElement = FpJsFormValidator.getErrorPathElement;
	FpJsFormValidator.getErrorPathElement = function (element) {
		return element;
	}
	
	SS6.clientSideValidation.init = function () {
		$('form :input:not([type="button"]):not([type="submit"]):not(.js-validation-loaded)')
			.each(SS6.clientSideValidation.inputBind)
			.addClass('js-validation-loaded');
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
	
	SS6.clientSideValidation.showErrors = function (errors, elementName) {
		console.log('asd');
		
		var $formConatiner = $(this).closest('.form-line');
		if ($formConatiner.size() > 0) {
			var $errorHighlight = $formConatiner;
		} else {
			$formConatiner = $(this).closest('.form-group, .js-form-group');
			var $errorHighlight = $(this);
		}
		var $errorList = $formConatiner.find('.js-validation-errors-list');
		var $errorListUl = $errorList.find('ul:first');
		var errorClass = 'js-' + elementName;
		$errorListUl.find('li:not([class]), li.' + errorClass).remove();
		
		if (errors.length > 0) {
			$errorHighlight.addClass('js-validation-error');
			$.each(errors, function (key, message) {
				$errorListUl.append($('<li/>').addClass(errorClass).text(message));
			});
			$errorList.show();
		} else {
			if ($errorListUl.find('li').size() === 0) {
				$errorHighlight.removeClass('js-validation-error');
				$errorList.hide();
			}
		}
	};
	
	$(document).ready(function () {
		SS6.clientSideValidation.init();
	});
})(jQuery);