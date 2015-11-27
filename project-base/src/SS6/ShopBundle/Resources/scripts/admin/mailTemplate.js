(function ($){

	SS6 = SS6 || {};
	SS6.mailTemplate = SS6.mailTemplate || {};

	SS6.mailTemplate.init = function () {
		$('.js-mail-template-toggle-container.js-toggle-container').each(function () {
			var $toggleContainer = $(this);
			var $toggleButton = $toggleContainer.find('.js-toggle-button');

			$toggleContainer.bind('showContent.toogleElement', function () {
				$toggleButton.text('-');
			});

			$toggleContainer.bind('hideContent.toogleElement', function () {
				$toggleButton.text('+');
			});
		});

		$('.js-mail-template-toggle-container.js-toggle-container:has(.js-validation-errors-list:not(.display-none))').each(function () {
			SS6.toggleElement.show($(this));
		});

		$('.js-send-mail-checkbox')
			.bind('change.requiredFields', SS6.mailTemplate.toggleRequiredFields)
			.trigger('change.requiredFields');
	};

	SS6.mailTemplate.toggleRequiredFields = function () {
		var sendMail = $(this).is(':checked');
		$(this).closest('.js-mail-template').find('.js-form-compulsory').toggle(sendMail);
	};

	$(document).ready(function () {
		SS6.mailTemplate.init();
	});

})(jQuery);
