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
		var $collection = $(collectionSelector);
		$($collection).jsFormValidator('delPrototype', itemIndex);
		SS6.validation.highlightSubmitButtons($collection.closest('form'));
		$collection.jsFormValidator('validate');
	};

	SS6.validation.isFormValid = function (form) {
		return $(form).find('.form-error:first, .js-validation-errors-list li[class]:first').size() === 0;
	};

	SS6.validation.ckeditorValidationInit = function (element) {
		$.each(element.children, function(index, childElement) {
			if (childElement.type === 'ckeditor') {
				CKEDITOR.instances[childElement.id].on('change', function() {
					$(childElement.domNode).jsFormValidator('validate');
				});
			}
			if (Object.keys(childElement.children).length > 0) {
				SS6.validation.ckeditorValidationInit(childElement);
			}
		});
	};

	FpJsFormValidator.customizeMethods._submitForm = FpJsFormValidator.customizeMethods.submitForm;
	FpJsFormValidator.customizeMethods.submitForm = function (event) {
		if (!$(this).hasClass('js-no-validate')) {
			FpJsFormValidator.customizeMethods._submitForm.call(this);
			if (!SS6.validation.isFormValid(this)) {
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

	FpJsFormValidator._getInputValue = FpJsFormValidator.getInputValue;
	FpJsFormValidator.getInputValue = function (element) {
		if (element.type === 'ckeditor') {
			return CKEDITOR.instances[element.id].getData();
		}
		return element.domNode ? element.domNode.value : undefined;
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
		SS6.validation.ckeditorValidationInit(element);

		return element;
	};

	var _SymfonyComponentValidatorConstraintsUrl = SymfonyComponentValidatorConstraintsUrl;
	SymfonyComponentValidatorConstraintsUrl = function () {
		this.message = '';

		this.validate = function (value, element) {
			var regexp = /^(https?:\/\/|(?=.*\.))([0-9a-z\u00C0-\u02FF\u0370-\u1EFF](([-0-9a-z\u00C0-\u02FF\u0370-\u1EFF]{0,61}[0-9a-z\u00C0-\u02FF\u0370-\u1EFF])?\.)*[a-z\u00C0-\u02FF\u0370-\u1EFF][-0-9a-z\u00C0-\u02FF\u0370-\u1EFF]{0,17}[a-z\u00C0-\u02FF\u0370-\u1EFF]|\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}|\[[0-9a-f:]{3,39}\])(:\d{1,5})?(\/\S*)?$/i;
			var errors = [];
			var f = FpJsFormValidator;

			if (!f.isValueEmty(value) && !regexp.test(value)) {
				errors.push(this.message.replace('{{ value }}', String('http://' + value)));
			}

			return errors;
		};
	};

})(jQuery);
