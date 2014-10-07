(function ($) {
	
	SS6 = window.SS6 || {};
	SS6.clientSideValidation = SS6.clientSideValidation || {};
	
	FpJsFormValidator.customizeMethods._submitForm = FpJsFormValidator.customizeMethods.submitForm;
	FpJsFormValidator.customizeMethods.submitForm = function (event) {
		if (!$(this).hasClass('js-no-validate')) {
			FpJsFormValidator.customizeMethods._submitForm.call(this, event);
			if ($(this).find('.js-validation-error:first').size() > 0) {
				event.preventDefault();
				alert('Překontrolujte prosím zadané hodnoty');
			}
		}
	};
	
	// some bug https://github.com/formapro/JsFormValidatorBundle/issues/61
	FpJsFormValidator._attachElement = FpJsFormValidator.attachElement;
	FpJsFormValidator.attachElement = function (element) {
		FpJsFormValidator._attachElement(element)
		if (!element.domNode) {
			return;
		}
		$(element.domNode).each(SS6.clientSideValidation.inputBind);
	};
	
	// stop error bubbling (problem in collections)
	FpJsFormValidator._getErrorPathElement = FpJsFormValidator.getErrorPathElement;
	FpJsFormValidator.getErrorPathElement = function (element) {
		return element;
	};
	
	FpJsFormValidator._initModel = FpJsFormValidator.initModel;
	FpJsFormValidator.initModel = function (model) {
		var element = this.createElement(model);
		if (!element) {
			return null;
		}
		var form = this.findFormElement(element);
		element.domNode = form;
		this.attachElement(element);
		if (form) {
			this.attachDefaultEvent(element, form);
		}

		return element;
	};
	
	$(document).ready(function () {
		$('.js-no-validate-button').click(function () {
			$(this).closest('form').addClass('js-no-validate');
		});
	});
	
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
	
})(jQuery);