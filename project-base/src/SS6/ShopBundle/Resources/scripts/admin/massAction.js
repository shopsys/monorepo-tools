(function ($) {

	SS6 = SS6 || {};
	SS6.massAction = SS6.massAction || {};

	SS6.massAction.init = function () {
		$('#js-mass-action-button').click(function () {
			$('#js-mass-action').toggleClass('active');
		});
	};

	$(document).ready(function () {
		SS6.massAction.init();
	});

})(jQuery);