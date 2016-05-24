(function ($) {

	SS6 = window.SS6 || {};
	SS6.validation = SS6.validation || {};

	SS6.validation.addNewItemToCollection = function (collectionSelector, itemIndex) {
		$($(collectionSelector)).jsFormValidator('addPrototype', itemIndex);
		SS6.formChangeInfo.showInfo();
	};

	SS6.validation.removeItemFromCollection = function (collectionSelector, itemIndex) {
		if (itemIndex === undefined) {
			throw Error('ItemIndex is undefined while remove item from collections');
		}
		var $collection = $(collectionSelector);
		$($collection).jsFormValidator('delPrototype', itemIndex);
		SS6.validation.highlightSubmitButtons($collection.closest('form'));
		$collection.jsFormValidator('validate');
		SS6.formChangeInfo.showInfo();
	};

	SS6.validation.isFormValid = function (form) {
		return $(form).find('.js-validation-errors-message').size() === 0;
	};

	SS6.validation.getErrorListClass = function (elementName) {
		return elementName.replace(/-/g, '_')
			.replace('form_error_', 'js-validation-error-list-')
			.replace('value_to_duplicates_', 'js-validation-error-list-'); // defined in function SymfonyComponentFormExtensionCoreDataTransformerValueToDuplicatesTransformer()
	};

	SS6.validation.ckeditorValidationInit = function (element) {
		$.each(element.children, function(index, childElement) {
			if (childElement.type === SS6.constant('\\SS6\\ShopBundle\\Form\\FormType::WYSIWYG')) {
				CKEDITOR.instances[childElement.id].on('change', function() {
					$(childElement.domNode).jsFormValidator('validate');
				});
			}
			if (Object.keys(childElement.children).length > 0) {
				SS6.validation.ckeditorValidationInit(childElement);
			}
		});
	};

	SS6.validation.elementBind = function (element) {
		if (!element.domNode) {
			return;
		}

		var $domNode = $(element.domNode);

		if ($domNode.closest('.js-no-validate').size() > 0) {
			return;
		}

		var isJsFileUpload = $domNode.closest('.js-file-upload').size() > 0;

		$domNode
			.bind('blur change', function (event) {
				if (this.jsFormValidator && isJsFileUpload === true) {
					event.preventDefault();
				} else {
					$(this).jsFormValidator('validate');

					if (this.jsFormValidator) {
						event.preventDefault();

						var parent = this.jsFormValidator.parent;
						while (parent) {
							parent.validate();

							parent = parent.parent;
						}
					}
				}
			})
			.focus(function () {
				$(this).closest('.form-input-error').removeClass('form-input-error');
			})
			.jsFormValidator({
				'showErrors': SS6.validation.showErrors
			});
	};

	FpJsFormValidator.customizeMethods._submitForm = FpJsFormValidator.customizeMethods.submitForm;

	// Custom behavior:
	// - disable JS validation for forms with class js-no-validate
	// - let the submit event propagate instead of stoping it and then calling item.submit()
	// - do not submit if custom "on-submit" code is specified
	// (the rest is copy&pasted from original method; eg. ajax validation)
	FpJsFormValidator.customizeMethods.submitForm = function (event) {
		if (!$(this).hasClass('js-no-validate')) {
			FpJsFormValidator.each(this, function (item) {
				var element = item.jsFormValidator;
				element.validateRecursively();
				if (FpJsFormValidator.ajax.queue) {
					FpJsFormValidator.ajax.callbacks.push(function () {
						element.onValidate.apply(element.domNode, [FpJsFormValidator.getAllErrors(element, {}), event]);
						if (element.isValid()) {
							item.submit();
						}
					});
				} else {
					element.onValidate.apply(element.domNode, [FpJsFormValidator.getAllErrors(element, {}), event]);
				}
			});
			if (!SS6.validation.isFormValid(this)) {
				event.preventDefault();
				SS6.validation.showFormErrorsWindow(this);
			} else if ($(this).data('on-submit') !== undefined) {
				$(this).trigger($(this).data('on-submit'));
				event.preventDefault();
			}
		}
	};

	// Bind custom events to each element with validator
	FpJsFormValidator._attachElement = FpJsFormValidator.attachElement;
	FpJsFormValidator.attachElement = function (element) {
		FpJsFormValidator._attachElement(element);
		SS6.validation.elementBind(element);
	};

	FpJsFormValidator._getElementValue = FpJsFormValidator.getElementValue;
	FpJsFormValidator.getElementValue = function (element) {
		var i = element.transformers.length;
		var value = this.getInputValue(element);

		if (i && undefined === value) {
			value = this.getMappedValue(element);
		} else if (
			element.type === SS6.constant('\\SS6\\ShopBundle\\Form\\FormType::COLLECTION')
			|| (Object.keys(element.children).length > 0 && element.type !== SS6.constant('\\SS6\\ShopBundle\\Form\\FormType::FILE_UPLOAD'))
		) {
			value = {};
			for (var childName in element.children) {
				value[childName] = this.getMappedValue(element.children[childName]);
			}
		} else {
			value = this.getSpecifiedElementTypeValue(element);
		}

		while (i--) {
			value = element.transformers[i].reverseTransform(value, element);
		}

		return value;
	};

	FpJsFormValidator._getInputValue = FpJsFormValidator.getInputValue;
	FpJsFormValidator.getInputValue = function (element) {
		if (element.type === SS6.constant('\\SS6\\ShopBundle\\Form\\FormType::WYSIWYG')) {
			return CKEDITOR.instances[element.id].getData();
		}
		if (element.type === SS6.constant('\\SS6\\ShopBundle\\Form\\FormType::FILE_UPLOAD')) {
			return $(element.domNode).find('.js-file-upload-uploaded-file').toArray();
		}
		if (element.type === SS6.constant('\\SS6\\ShopBundle\\Form\\FormType::PRODUCTS')) {
			var value = [];
			$(element.domNode).find('.js-products-picker-item-input').each(function () {
				value.push($(this).val());
			});
			return value;
		}
		return FpJsFormValidator._getInputValue(element);
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

	// disable JS validation for form fields in element with class js-no-validate
	FpJsFormValidator._createElement = FpJsFormValidator.createElement;
	FpJsFormValidator.createElement = function (model) {
		var element = this._createElement(model);
		if (!element) {
			return null;
		}
		if ($(element.domNode).closest('.js-no-validate').size() > 0) {
			return null;
		}

		return element;
	};

	// reported as https://github.com/formapro/JsFormValidatorBundle/issues/66
	FpJsFormValidator._checkValidationGroups = FpJsFormValidator.checkValidationGroups;
	FpJsFormValidator.checkValidationGroups = function (needle, haystack) {
		if (typeof haystack === 'undefined') {
			haystack = [SS6.constant('Symfony\\Component\\Validator\\Constraint::DEFAULT_GROUP')];
		}
		return FpJsFormValidator._checkValidationGroups(needle, haystack);
	};

	// determine domElement as the closest ancestor of all children
	FpJsFormValidator._findDomElement = FpJsFormValidator.findDomElement;
	FpJsFormValidator.findDomElement = function (model) {
		return SS6.validation.findDomElementRecursive(model);
	};

	SS6.validation.findDomElementRecursive = function (model) {
		var domElement = FpJsFormValidator._findDomElement(model);

		if (domElement !== null) {
			return domElement;
		}

		var childDomElements = [];
		for (var i in model.children) {
			var child = model.children[i];
			var childDomElement = SS6.validation.findDomElementRecursive(child);

			if (childDomElement !== null) {
				childDomElements.push(childDomElement);
			}
		}

		return SS6.validation.findClosestCommonAncestor(childDomElements);
	};

	SS6.validation.findClosestCommonAncestor = function (domElements) {
		if (domElements.length === 0) {
			return null;
		}

		var domElementsAncestors = [];

		for (var i in domElements) {
			var domElement = domElements[i];
			var $domElementParents = $(domElement).parents();

			var domElementAncestors = SS6.validation.reverseCollectionToArray($domElementParents);

			domElementsAncestors.push(domElementAncestors);
		}

		var firstDomElementAncestors = domElementsAncestors[0];

		var closestCommonAncestor = null;
		for (var ancestorLevel = 0; ancestorLevel < firstDomElementAncestors.length; ancestorLevel++) {
			if (firstDomElementAncestors[ancestorLevel].tagName.toLowerCase() !== 'form') {
				for (var i = 1; i < domElementsAncestors.length; i++) {
					if (domElementsAncestors[i][ancestorLevel] !== firstDomElementAncestors[ancestorLevel]) {
						return closestCommonAncestor;
					}
				}

				closestCommonAncestor = firstDomElementAncestors[ancestorLevel];
			}
		}

		return closestCommonAncestor;
	};

	SS6.validation.reverseCollectionToArray = function ($collection) {
		var result = [];

		for (var i = $collection.size() - 1; i >= 0; i--) {
			result.push($collection[i]);
		}

		return result;
	};

	var _SymfonyComponentValidatorConstraintsUrl = SymfonyComponentValidatorConstraintsUrl;
	SymfonyComponentValidatorConstraintsUrl = function () {
		this.message = '';

		this.validate = function (value, element) {
			var regexp = /^(https?:\/\/|(?=.*\.))([0-9a-z\u00C0-\u02FF\u0370-\u1EFF](([-0-9a-z\u00C0-\u02FF\u0370-\u1EFF]{0,61}[0-9a-z\u00C0-\u02FF\u0370-\u1EFF])?\.)*[a-z\u00C0-\u02FF\u0370-\u1EFF][-0-9a-z\u00C0-\u02FF\u0370-\u1EFF]{0,17}[a-z\u00C0-\u02FF\u0370-\u1EFF]|\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}|\[[0-9a-f:]{3,39}\])(:\d{1,5})?(\/\S*)?$/i;
			var errors = [];
			if (!FpJsFormValidator.isValueEmty(value) && !regexp.test(value)) {
				errors.push(this.message.replace('{{ value }}', String('http://' + value)));
			}

			return errors;
		};
	};

	SS6.validation.isExpandedChoiceFormType = function(element, value) {
		return element.type === SS6.constant('SS6\\ShopBundle\\Form\\FormType::CHOICE') && !$.isArray(value);
	};

	SS6.validation.isExpandedChoiceEmpty = function(value) {
		var isEmpty = true;

		$.each(value, function(key, value) {
			if (value !== false) {
				isEmpty = false;
				return false;
			}
		});

		return isEmpty;
	};

	FpJsFormValidator._isValueEmty = FpJsFormValidator.isValueEmty;
	FpJsFormValidator.isValueEmty = function (value, element) {
		if (element instanceof FpJsFormElement) {
			if (SS6.validation.isExpandedChoiceFormType(element, value)) {
				return SS6.validation.isExpandedChoiceEmpty(value);
			}
		}

		return FpJsFormValidator._isValueEmty(value);
	};

	SS6.validation.showErrors = function (errors, elementName) {
		var $errorList = SS6.validation.findOrCreateErrorList($(this), elementName);
		var $errorListUl = $errorList.find('ul:first');
		var $elementsToHighlight = SS6.validation.findElementsToHighlight($(this));

		var elementErrorClass = 'js-' + elementName;
		$errorListUl.find('li').remove();

		if (errors.length > 0) {
			$elementsToHighlight.addClass('form-input-error');
			$.each(errors, function (key, message) {
				$errorListUl
					.append($('<li/>')
					.addClass('js-validation-errors-message')
					.addClass(elementErrorClass)
					.text(message));
			});
			$errorList.show();
		} else if ($errorListUl.find('li').size() === 0) {
			$elementsToHighlight.removeClass('form-input-error');
			$errorList.hide();
		}

		SS6.validation.highlightSubmitButtons($(this).closest('form'));
	};

	SS6.validation.findOrCreateErrorList = function ($formInput, elementName) {
		var errorListClass = SS6.validation.getErrorListClass(elementName);
		var $errorList = $('.' + errorListClass);
		if ($errorList.size() === 0) {
			$errorList = $($.parseHTML(
				'<div class="in-message in-message--danger js-validation-errors-list ' + errorListClass + '">\
					<ul class="in-message__list"></ul>\
				</div>'
			));
			$errorList.insertBefore($formInput);
		}

		return $errorList;
	};

	SS6.validation.showFormErrorsWindow = function (container) {
		var $formattedFormErrors = SS6.validation.getFormattedFormErrors(container);

		SS6.window({
			content:
				'<div class="text-left">'
				+ SS6.translator.trans('Překontrolujte prosím zadané hodnoty.<br><br>')
				+ $formattedFormErrors[0].outerHTML
				+ '</div>'
		});
	};

	SS6.validation.getFormattedFormErrors = function (container) {
		var errorsByLabel = SS6.validation.getFormErrorsIndexedByLabel(container);
		var $formattedFormErrors = $('<ul/>');
		for (var label in errorsByLabel) {
			var $errorsUl = $('<ul/>');
			for (var i in errorsByLabel[label]) {
				$errorsUl.append($('<li/>').text(errorsByLabel[label][i]));
			}
			$formattedFormErrors.append($('<li/>').text(label).append($errorsUl));
		}

		return $formattedFormErrors;
	};

	SS6.validation.getInputIdByErrorList = function($errorList) {
		var inputIdMatch = $errorList.attr('class').match(/js\-validation\-error\-list\-([^\s]+)/);
		if (inputIdMatch) {
			return inputIdMatch[1];
		}

		return undefined;
	};

	SS6.validation.getFormErrorsIndexedByLabel = function (container) {
		var errorsByLabel = {};

		$(container).find('.js-validation-errors-list li').each(function () {
			var $errorList = $(this).closest('.js-validation-errors-list');
			var errorMessage = $(this).text();
			var inputId = SS6.validation.getInputIdByErrorList($errorList);

			if (inputId !== undefined) {
				var $label = SS6.validation.findLabelByInputId(inputId);
				if ($label.size() > 0) {
					errorsByLabel = SS6.validation.addLabelError(errorsByLabel, $label.text(), errorMessage);
				}
			}
		});

		return errorsByLabel;
	};

	SS6.validation.findLabelByInputId = function (inputId) {
		var $label = $('label[for="' + inputId + '"]');
		var $input = $('#' + inputId);

		if ($label.size() === 0) {
			$label = SS6.validation.getClosestLabel($input, '.js-validation-label');
		}
		if ($label.size() === 0) {
			$label = SS6.validation.getClosestLabel($input, 'label');
		}
		if ($label.size() === 0) {
			$label = SS6.validation.getClosestLabel($input, '.form-full__title');
		}

		return $label;
	};

	SS6.validation.getClosestLabel = function ($input, selector) {
		var $formLine = $input.closest('.form-line:has(' + selector + '), .js-form-group:has(' + selector + '), .form-full:has(' + selector + ')');
		return $formLine.find(selector).filter(':first');
	};

	SS6.validation.addLabelError = function(errorsByLabel, labelText, errorMessage) {
		labelText = SS6.validation.normalizeLabelText(labelText);

		if (errorsByLabel[labelText] === undefined) {
			errorsByLabel[labelText] = [];
		}
		if (errorsByLabel[labelText].indexOf(errorMessage) === -1) {
			errorsByLabel[labelText].push(errorMessage);
		}

		return errorsByLabel;
	};

	SS6.validation.normalizeLabelText = function (labelText) {
		return labelText.replace(/^\s*(.*)[\s:\*]*$/, '$1');
	};

})(jQuery);
