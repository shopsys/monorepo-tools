(function ($) {

	SS6 = window.SS6 || {};
	SS6.termsAndConditions = SS6.termsAndConditions || {};


	SS6.termsAndConditions.init = function () {
		$('#js-terms-and-conditions-print').on('click', function () {
			window.frames['js-terms-and-conditions-frame'].print();
		});
	};

	$(document).ready(function () {
		SS6.termsAndConditions.init();
	});

})(jQuery);
