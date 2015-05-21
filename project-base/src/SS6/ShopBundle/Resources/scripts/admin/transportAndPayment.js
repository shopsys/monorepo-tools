(function ($) {

	SS6 = window.SS6 || {};
	SS6.transportAndPayment = SS6.transportAndPayment || {};

	SS6.transportAndPayment.init = function () {
		var $gridSaveButtons = $('.js-drag-and-drop-grid-submit');
		var $gridsOnPage = $('.grid');
		var $saveAllButton = $('.js-drag-and-drop-grid-submit-all');

		if ($saveAllButton.size() === 1) {
			$gridSaveButtons.toggle(false);

			$gridsOnPage.on('update', function() {
				SS6.formChangeInfo.showInfo();
				$saveAllButton.removeClass('btn-disabled');
			});

			$gridsOnPage.on('save', function() {
				SS6.formChangeInfo.removeInfo();
				$saveAllButton.addClass('btn-disabled');
			});

			$saveAllButton.click(function() {
				$gridSaveButtons.click();
			});
		}
	};

	$(document).ready(function () {
		SS6.transportAndPayment.init();
	});

})(jQuery);