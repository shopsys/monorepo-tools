(function ($) {

	SS6 = window.SS6 || {};
	SS6.entityUrls = SS6.entityUrls || {};

	SS6.entityUrls.Widget = function ($entityUrls) {
		var $buttonAdd = $entityUrls.find('.js-entity-urls-button-add-url');
		var windowContentHtml = $buttonAdd.data('window-content-template');

		this.init = function () {
			$buttonAdd.click(openWindow);
			$entityUrls.on('click', '.js-entity-urls-new-row-delete-button', onClickRemoveNewUrl);
		};

		var openWindow = function () {
			var $window = SS6.window({
				content: windowContentHtml
			});
			var newUrlWindow = new SS6.entityUrls.NewUrlWindow($window, $entityUrls);
			newUrlWindow.init();
		};

		var onClickRemoveNewUrl = function () {
			$(this).closest('.js-entity-urls-new-row').remove();
		};
	};

	$(document).ready(function () {
		$('.js-entity-urls').each(function () {
			var entityUrlsWidget = new SS6.entityUrls.Widget($(this));
			entityUrlsWidget.init();
		});
	});

})(jQuery);
