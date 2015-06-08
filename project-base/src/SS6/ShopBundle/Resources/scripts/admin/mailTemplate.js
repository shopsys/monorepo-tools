(function ($){

	SS6 = SS6 || {};
	SS6.mailTemplate = SS6.mailTemplate || {};

	SS6.mailTemplate.init = function () {
		$('#js-mail-templates .toggle-container:has(.js-validation-errors-list:not(.display-none))').each(function () {
			SS6.toggleElement.show($(this));
		});
	};

	$(document).ready(function () {
		SS6.mailTemplate.init();
	});

})(jQuery);
