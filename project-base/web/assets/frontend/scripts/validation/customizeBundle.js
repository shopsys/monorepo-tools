(function ($) {
	
	SS6 = window.SS6 || {};
	SS6.validation = SS6.validation || {};
	
	SS6.validation.addNewItemToCollection = function (collectionSelector, itemIndex) {
		$($(collectionSelector)).jsFormValidator('addPrototype', itemIndex);
	};
	
	SS6.validation.removeItemFromCollection = function (collectionSelector, itemIndex) {
		if (itemIndex === undefined) {
			throw Error('ItemIndex is undefined while remove item from collections');
		}
		$($(collectionSelector)).jsFormValidator('delPrototype', itemIndex);
		SS6.validation.highlightSubmitButtons($(collectionSelector).closest('form'));
	};
	
	FpJsFormValidator.customizeMethods._submitForm = FpJsFormValidator.customizeMethods.submitForm;
	FpJsFormValidator.customizeMethods.submitForm = function (event) {
		if (!$(this).hasClass('js-no-validate')) {
			FpJsFormValidator.customizeMethods._submitForm.call(this, event);
			if ($(this).find('.js-validation-error:first, js-validation-errors-list li[class]:first').size() > 0) {
				event.preventDefault();
				SS6.window({
					content: "Překontrolujte prosím zadané hodnoty."
				});
			}
		}
	};
	
	// Bind custom events to each element with validator
	FpJsFormValidator._attachElement = FpJsFormValidator.attachElement;
	FpJsFormValidator.attachElement = function (element) {
		FpJsFormValidator._attachElement(element);
		if (!element.domNode) {
			return;
		}
		$(element.domNode).each(SS6.validation.inputBind);
	};
	
	// stop error bubbling, because errors of some collections (eg. admin order items) bubble to main form and mark all inputs as invalid
	FpJsFormValidator._getErrorPathElement = FpJsFormValidator.getErrorPathElement;
	FpJsFormValidator.getErrorPathElement = function (element) {
		return element;
	};
	
	// some forms (eg. frontend order transport and payments) throws "Uncaught TypeError: Cannot read property 'domNode' of null"
	// reported as https://github.com/formapro/JsFormValidatorBundle/issues/61
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
	
})(jQuery);