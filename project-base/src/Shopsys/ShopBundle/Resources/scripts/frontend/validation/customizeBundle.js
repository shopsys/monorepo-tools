(function ($) {

    Shopsys = window.Shopsys || {};
    Shopsys.validation = Shopsys.validation || {};

    Shopsys.validation.addNewItemToCollection = function (collectionSelector, itemIndex) {
        $($(collectionSelector)).jsFormValidator('addPrototype', itemIndex);
        Shopsys.formChangeInfo.showInfo();
    };

    Shopsys.validation.removeItemFromCollection = function (collectionSelector, itemIndex) {
        if (itemIndex === undefined) {
            throw Error('ItemIndex is undefined while remove item from collections');
        }
        var $collection = $(collectionSelector);
        $($collection).jsFormValidator('delPrototype', itemIndex);
        Shopsys.validation.highlightSubmitButtons($collection.closest('form'));
        $collection.jsFormValidator('validate');
        Shopsys.formChangeInfo.showInfo();
    };

    Shopsys.validation.isFormValid = function (form) {
        return $(form).find('.js-validation-errors-message').length === 0;
    };

    Shopsys.validation.getErrorListClass = function (elementName) {
        return elementName.replace(/-/g, '_')
            .replace('form_error_', 'js-validation-error-list-')
            .replace('value_to_duplicates_', 'js-validation-error-list-'); // defined in function SymfonyComponentFormExtensionCoreDataTransformerValueToDuplicatesTransformer()
    };

    Shopsys.validation.ckeditorValidationInit = function (element) {
        $.each(element.children, function(index, childElement) {
            if (childElement.type === Shopsys.constant('\\Shopsys\\ShopBundle\\Form\\FormType::WYSIWYG')) {
                CKEDITOR.instances[childElement.id].on('change', function() {
                    $(childElement.domNode).jsFormValidator('validate');
                });
            }
            if (Object.keys(childElement.children).length > 0) {
                Shopsys.validation.ckeditorValidationInit(childElement);
            }
        });
    };

    Shopsys.validation.elementBind = function (element) {
        if (!element.domNode) {
            return;
        }

        var $domNode = $(element.domNode);

        if ($domNode.closest('.js-no-validate').length > 0) {
            return;
        }

        var isJsFileUpload = $domNode.closest('.js-file-upload').length > 0;

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
                'showErrors': Shopsys.validation.showErrors
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
            if (!Shopsys.validation.isFormValid(this)) {
                event.preventDefault();
                Shopsys.validation.showFormErrorsWindow(this);
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
        Shopsys.validation.elementBind(element);
    };

    FpJsFormValidator._getElementValue = FpJsFormValidator.getElementValue;
    FpJsFormValidator.getElementValue = function (element) {
        var i = element.transformers.length;
        var value = this.getInputValue(element);

        if (i && undefined === value) {
            value = this.getMappedValue(element);
        } else if (
            element.type === Shopsys.constant('\\Shopsys\\ShopBundle\\Form\\FormType::COLLECTION')
            || (Object.keys(element.children).length > 0 && element.type !== Shopsys.constant('\\Shopsys\\ShopBundle\\Form\\FormType::FILE_UPLOAD'))
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
        if (element.type === Shopsys.constant('\\Shopsys\\ShopBundle\\Form\\FormType::WYSIWYG')) {
            return CKEDITOR.instances[element.id].getData();
        }
        if (element.type === Shopsys.constant('\\Shopsys\\ShopBundle\\Form\\FormType::FILE_UPLOAD')) {
            return $(element.domNode).find('.js-file-upload-uploaded-file').toArray();
        }
        if (element.type === Shopsys.constant('\\Shopsys\\ShopBundle\\Form\\FormType::PRODUCTS')) {
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
        Shopsys.validation.ckeditorValidationInit(element);

        return element;
    };

    // disable JS validation for form fields in element with class js-no-validate
    FpJsFormValidator._createElement = FpJsFormValidator.createElement;
    FpJsFormValidator.createElement = function (model) {
        var element = this._createElement(model);
        if (!element) {
            return null;
        }
        if ($(element.domNode).closest('.js-no-validate').length > 0) {
            return null;
        }

        return element;
    };

    // reported as https://github.com/formapro/JsFormValidatorBundle/issues/66
    FpJsFormValidator._checkValidationGroups = FpJsFormValidator.checkValidationGroups;
    FpJsFormValidator.checkValidationGroups = function (needle, haystack) {
        if (typeof haystack === 'undefined') {
            haystack = [Shopsys.constant('Symfony\\Component\\Validator\\Constraint::DEFAULT_GROUP')];
        }
        return FpJsFormValidator._checkValidationGroups(needle, haystack);
    };

    // determine domElement as the closest ancestor of all children
    FpJsFormValidator._findDomElement = FpJsFormValidator.findDomElement;
    FpJsFormValidator.findDomElement = function (model) {
        return Shopsys.validation.findDomElementRecursive(model);
    };

    Shopsys.validation.findDomElementRecursive = function (model) {
        var domElement = FpJsFormValidator._findDomElement(model);

        if (domElement !== null) {
            return domElement;
        }

        var childDomElements = [];
        for (var i in model.children) {
            var child = model.children[i];
            var childDomElement = Shopsys.validation.findDomElementRecursive(child);

            if (childDomElement !== null) {
                childDomElements.push(childDomElement);
            }
        }

        return Shopsys.validation.findClosestCommonAncestor(childDomElements);
    };

    Shopsys.validation.findClosestCommonAncestor = function (domElements) {
        if (domElements.length === 0) {
            return null;
        }

        var domElementsAncestors = [];

        for (var i in domElements) {
            var domElement = domElements[i];
            var $domElementParents = $(domElement).parents();

            var domElementAncestors = Shopsys.validation.reverseCollectionToArray($domElementParents);

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

    Shopsys.validation.reverseCollectionToArray = function ($collection) {
        var result = [];

        for (var i = $collection.length - 1; i >= 0; i--) {
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

    Shopsys.validation.isExpandedChoiceFormType = function(element, value) {
        return element.type === Shopsys.constant('Shopsys\\ShopBundle\\Form\\FormType::CHOICE') && !$.isArray(value);
    };

    Shopsys.validation.isExpandedChoiceEmpty = function(value) {
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
            if (Shopsys.validation.isExpandedChoiceFormType(element, value)) {
                return Shopsys.validation.isExpandedChoiceEmpty(value);
            }
        }

        return FpJsFormValidator._isValueEmty(value);
    };

    Shopsys.validation.showErrors = function (errors, sourceId) {
        var $errorList = Shopsys.validation.findOrCreateErrorList($(this), sourceId);
        var $errorListUl = $errorList.find('ul:first');
        var $elementsToHighlight = Shopsys.validation.findElementsToHighlight($(this));

        var errorSourceClass = 'js-error-source-id-' + sourceId;
        $errorListUl.find('li').remove();

        if (errors.length > 0) {
            $elementsToHighlight.addClass('form-input-error');
            $.each(errors, function (key, message) {
                $errorListUl.append(
                    $('<li/>')
                        .addClass('js-validation-errors-message')
                        .addClass(errorSourceClass)
                        .text(message)
                );
            });
            $errorList.show();
        } else if ($errorListUl.find('li').length === 0) {
            $elementsToHighlight.removeClass('form-input-error');
            $errorList.hide();
        }

        Shopsys.validation.highlightSubmitButtons($(this).closest('form'));
    };

    Shopsys.validation.findOrCreateErrorList = function ($formInput, elementName) {
        var errorListClass = Shopsys.validation.getErrorListClass(elementName);
        var $errorList = $('.' + errorListClass);
        if ($errorList.length === 0) {
            $errorList = $($.parseHTML(
                '<div class="in-message in-message--danger js-validation-errors-list ' + errorListClass + '">\
                    <ul class="in-message__list"></ul>\
                </div>'
            ));
            $errorList.insertBefore($formInput);
        }

        return $errorList;
    };

    Shopsys.validation.showFormErrorsWindow = function (container) {
        var $formattedFormErrors = Shopsys.validation.getFormattedFormErrors(container);

        Shopsys.window({
            content:
                '<div class="text-left">'
                + Shopsys.translator.trans('Please check the entered values.<br><br>')
                + $formattedFormErrors[0].outerHTML
                + '</div>'
        });
    };

    Shopsys.validation.getFormattedFormErrors = function (container) {
        var errorsByLabel = Shopsys.validation.getFormErrorsIndexedByLabel(container);
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

    Shopsys.validation.getInputIdByErrorList = function($errorList) {
        var inputIdMatch = $errorList.attr('class').match(/js\-validation\-error\-list\-([^\s]+)/);
        if (inputIdMatch) {
            return inputIdMatch[1];
        }

        return undefined;
    };

    Shopsys.validation.getFormErrorsIndexedByLabel = function (container) {
        var errorsByLabel = {};

        $(container).find('.js-validation-errors-list li').each(function () {
            var $errorList = $(this).closest('.js-validation-errors-list');
            var errorMessage = $(this).text();
            var inputId = Shopsys.validation.getInputIdByErrorList($errorList);

            if (inputId !== undefined) {
                var $label = Shopsys.validation.findLabelByInputId(inputId);
                if ($label.length > 0) {
                    errorsByLabel = Shopsys.validation.addLabelError(errorsByLabel, $label.text(), errorMessage);
                }
            }
        });

        return errorsByLabel;
    };

    Shopsys.validation.findLabelByInputId = function (inputId) {
        var $label = $('label[for="' + inputId + '"]');
        var $input = $('#' + inputId);

        if ($label.length === 0) {
            $label = $('#js-label-' + inputId);
        }
        if ($label.length === 0) {
            $label = Shopsys.validation.getClosestLabel($input, '.js-validation-label');
        }
        if ($label.length === 0) {
            $label = Shopsys.validation.getClosestLabel($input, 'label');
        }
        if ($label.length === 0) {
            $label = Shopsys.validation.getClosestLabel($input, '.form-full__title');
        }

        return $label;
    };

    Shopsys.validation.getClosestLabel = function ($input, selector) {
        var $formLine = $input.closest('.form-line:has(' + selector + '), .js-form-group:has(' + selector + '), .form-full:has(' + selector + ')');
        return $formLine.find(selector).filter(':first');
    };

    Shopsys.validation.addLabelError = function(errorsByLabel, labelText, errorMessage) {
        labelText = Shopsys.validation.normalizeLabelText(labelText);

        if (errorsByLabel[labelText] === undefined) {
            errorsByLabel[labelText] = [];
        }
        if (errorsByLabel[labelText].indexOf(errorMessage) === -1) {
            errorsByLabel[labelText].push(errorMessage);
        }

        return errorsByLabel;
    };

    Shopsys.validation.normalizeLabelText = function (labelText) {
        return labelText.replace(/^\s*(.*)[\s:\*]*$/, '$1');
    };

})(jQuery);
