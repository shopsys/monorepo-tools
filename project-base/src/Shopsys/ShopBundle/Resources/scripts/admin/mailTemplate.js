(function ($){

	Shopsys = Shopsys || {};
	Shopsys.mailTemplate = Shopsys.mailTemplate || {};

	Shopsys.mailTemplate.init = function ($container) {
		$container.filterAllNodes('.js-mail-template-toggle-container.js-toggle-container').each(function () {
			var $toggleContainer = $(this);
			var $toggleButton = $toggleContainer.find('.js-toggle-button');

			$toggleContainer.bind('showContent.toggleElement', function () {
				$toggleButton.text('-');
			});

			$toggleContainer.bind('hideContent.toggleElement', function () {
				$toggleButton.text('+');
			});
		});

		$container.filterAllNodes('.js-mail-template-toggle-container.js-toggle-container:has(.js-validation-errors-list:not(.display-none))').each(function () {
			Shopsys.toggleElement.show($(this));
		});

		$container.filterAllNodes('.js-send-mail-checkbox')
			.bind('change.requiredFields', Shopsys.mailTemplate.toggleRequiredFields)
			.trigger('change.requiredFields');
	};

	Shopsys.mailTemplate.toggleRequiredFields = function () {
		var sendMail = $(this).is(':checked');
		$(this).closest('.js-mail-template').find('.js-form-compulsory').toggle(sendMail);
	};

	Shopsys.register.registerCallback(Shopsys.mailTemplate.init);

})(jQuery);
