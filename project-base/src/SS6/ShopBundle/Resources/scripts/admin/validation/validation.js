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
		$errorListUl.find('li:not([class]), li.' + elementErrorClass).remove();

		if (errors.length > 0) {
			$elementsToHighlight.addClass('form-error');
			$.each(errors, function (key, message) {
				$errorListUl.append($('<li/>').addClass(elementErrorClass).text(message));
			});
			$errorList.show();
		} else if ($errorListUl.find('li').size() === 0) {
			$elementsToHighlight.removeClass('form-error');
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
		return $formInput.filter('input, select, textarea, .form-line');
	};

	SS6.validation.highlightSubmitButtons = function($form){
		var $submitButtons = $form.find('.btn-primary[type="submit"]');

		if (SS6.validation.isFormValid($form)) {
			$submitButtons.removeClass('btn-disabled');
		} else {
			$submitButtons.addClass('btn-disabled');
		}
	};

})(jQuery);
