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

	SS6.validation.showErrors = function (errors, elementName) {
		var $errorList = SS6.validation.findOrCreateErrorList($(this), elementName);
		var $errorListUl = $errorList.find('ul:first');
		var $elementsToHighlight = SS6.validation.findElementsToHighlight($(this), elementName);

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

	SS6.validation.getInputIdByErrorList = function($errorList) {
		var inputIdMatch = $errorList.attr('class').match(/js\-validation\-error\-list\-([^\s]+)/);
		if (inputIdMatch) {
			return inputIdMatch[1];
		}

		return undefined;
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

	SS6.validation.showFormErrorsWindow = function (container) {
		var $formattedFormErrors = SS6.validation.getFormattedFormErrors(container);

		SS6.window({
			content:
				'<div class="h-text-left">'
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

	$(document).ready(function () {
		var $formattedFormErrors = SS6.validation.getFormattedFormErrors(document);
		$('.js-flash-message.in-message--danger').append($formattedFormErrors);
	});

})(jQuery);
