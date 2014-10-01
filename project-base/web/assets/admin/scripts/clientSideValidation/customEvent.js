(function ($) {
	
	SS6 = window.SS6 || {};
	SS6.clientSideValidation = SS6.clientSideValidation || {};
	
	SS6.clientSideValidation.init = function () {
		$('form').find('input, textarea, select').each(SS6.clientSideValidation.inputBind);
	}
	
	SS6.clientSideValidation.inputBind = function () {
		$(this).bind('blur change', function () {
				$(this).jsFormValidator('validate')
			})
			.focus(function () {
				$(this).closest('.js-validation-error').removeClass('js-validation-error');
			})
			.jsFormValidator({
				'showErrors': SS6.clientSideValidation.showErrors
			});
	}
	
	SS6.clientSideValidation.showErrors = function (errors) {
		var $formLine = $(this).closest('.form-line');
		var $errorList = $formLine.find('.js-validation-errors-list');
		var $errorListUl = $errorList.find('ul:first');
		if (errors.length) {
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
	}
	
	$(document).ready(function () {
		SS6.clientSideValidation.init();
	});
})(jQuery);