(function ($) {

	SS6 = window.SS6 || {};
	SS6.order = SS6.order || {};

	SS6.order.OrderPreview = function ($orderPreview) {
		var $previewIcon = $orderPreview.find('.js-order-preview-icon');
		var $previewBox = $orderPreview.find('.js-order-preview-box');

		var url = $orderPreview.data('preview-url');
		var isLoading = false;
		var isLoaded = false;
		var showInWindowAfterLoad = false;

		this.init = function () {
			$previewIcon
				.mouseenter(function() {
					$previewBox.show();
				})
				.mouseleave(function () {
					$previewBox.hide();
				})
				.click(function () {
					if (isLoaded) {
						showInWindow();
					} else {
						showInWindowAfterLoad = true;
					}
				})
				.hoverIntent({
					interval: 100,
					over: function () {
						if (!isLoaded && !isLoading) {
							isLoading = true;
							$.ajax({
								url: url,
								success: onLoadPreview
							});
						}
					},
					out: function () {}
				});
		};

		var showInWindow = function () {
			SS6.window({
				content: $previewBox.html(),
				wide: true
			});
		};

		var onLoadPreview = function (responseHtml) {
			isLoading = false;
			isLoaded = true;
			$previewBox.html(responseHtml);
			if (showInWindowAfterLoad) {
				showInWindow();
			}
		}
	};

	$(document).ready(function () {
		$('.js-order-preview').each(function () {
			var orderPreview = new SS6.order.OrderPreview($(this));
			orderPreview.init();
		});
	});

})(jQuery);
