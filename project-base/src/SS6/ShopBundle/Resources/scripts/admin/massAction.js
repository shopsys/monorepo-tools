(function ($) {

	SS6 = SS6 || {};
	SS6.massAction = SS6.massAction || {};

	SS6.massAction.init = function ($container) {
		$container.filterAllNodes('#js-mass-action-button').click(function () {
			$('#js-mass-action').toggleClass('active');
		});
	};

	SS6.register.registerCallback(SS6.massAction.init);

})(jQuery);