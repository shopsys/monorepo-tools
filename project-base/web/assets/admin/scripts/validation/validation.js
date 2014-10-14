(function ($) {

	SS6 = window.SS6 || {};
	SS6.validation = SS6.validation || {};

	$(document).ready(function () {
		$('.js-no-validate-button').click(function () {
			$(this).closest('form').addClass('js-no-validate');
		});
	});

	SS6.validation.inputBind = function () {
		$(this)
			.bind('blur change', function () {
				$(this).jsFormValidator('validate')
			})
			.focus(function () {
				$(this).closest('.js-validation-error').removeClass('js-validation-error');
			})
			.jsFormValidator({
				'showErrors': SS6.validation.showErrors
			});
	};

	SS6.validation.showErrors = function (errors, elementName) {

		var $elementsToHighlight = SS6.validation.findElementsToHighlight($(this));
		var $errorList = SS6.validation.findErrorList($(this));
		var $errorListUl = $errorList.find('ul:first');

		var errorClass = 'js-' + elementName;
		$errorListUl.find('li:not([class]), li.' + errorClass).remove();

		if (errors.length > 0) {
			$elementsToHighlight.addClass('js-validation-error');
			$.each(errors, function (key, message) {
				$errorListUl.append($('<li/>').addClass(errorClass).text(message));
			});
			$errorList.show();
		} else {
			if ($errorListUl.find('li').size() === 0) {
				$elementsToHighlight.removeClass('js-validation-error');
				$errorList.hide();
			}
		}

		SS6.validation.highlightSubmitButtons($(this).closest('form'));
	};

	SS6.validation.findFormContainer = function ($formInput) {
		var $formConatiner = $formInput.closest('.form-line');
		if ($formConatiner.size() === 0) {
			return $formInput.closest('.form-group, .js-form-group');
		}

		return $formConatiner;
	}

	SS6.validation.findErrorList = function ($formInput) {
		var $formConatiner = SS6.validation.findFormContainer($formInput);
		return $formConatiner.find('.js-validation-errors-list:first');
	};

	SS6.validation.findElementsToHighlight = function ($formInput) {
		var $formConatiner = SS6.validation.findFormContainer($formInput);
		if ($formConatiner.hasClass('form-line')) {
			var $elementsToHighlight = $formConatiner;
		} else {
			var $elementsToHighlight =  $formInput;
		}

		return $elementsToHighlight.filter('input, select, textarea, .form-line');
	};

	SS6.validation.highlightSubmitButtons = function($form){
		var $submitButtons = $form.find('.btn-primary[type="submit"]');

		if ($form.find('.js-validation-error:first').size() > 0) {
			$submitButtons.addClass('disabled');
		} else {
			$submitButtons.removeClass('disabled');
		}
	};

})(jQuery);