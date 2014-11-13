(function ($){

	SS6 = SS6 || {};
	SS6.mailTemplate = SS6.mailTemplate || {};

	SS6.mailTemplate.init = function () {
		$('#js-mail-templates .toggle-container:has(.js-validation-errors-list:not(.js-hidden))').each(function () {
			var toggleContainer = $(this);
			SS6.toggleElement.show(toggleContainer);
		});
	};

	$(document).ready(function () {
		SS6.mailTemplate.init();
	});

})(jQuery);
