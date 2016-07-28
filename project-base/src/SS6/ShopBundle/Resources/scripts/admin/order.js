(function ($) {

	SS6 = window.SS6 || {};
	SS6.order = SS6.order || {};

	SS6.order.OrderPreview = function ($orderPreview) {
		var $previewIcon = $orderPreview.find('.js-order-preview-icon');
		var $previewBox = $orderPreview.find('.js-order-preview-box');
		var $previewBoxWindow = $previewBox.find('.js-order-preview-box-window');

		var url = $orderPreview.data('preview-url');
		var isLoading = false;
		var isLoaded = false;
		var showInWindowAfterLoad = false;

		this.init = function () {
			var keepVisible = false;

			$previewIcon
				.mouseleave(function () {
					keepVisible = false;
					setTimeout(function () {
						if (!keepVisible) {
							$previewBox.hide();
						}
					}, 20); // Mouse needs some time to leave the icon and enter the $visibilityBox
				})
				.click(function () {
					if (isLoaded) {
						showInWindow();
					} else {
						showInWindowAfterLoad = true;
					}
				})
				.hoverIntent({
					interval: 200,
					over: function () {
						$previewBox.show();
						if (!isLoaded && !isLoading) {
							isLoading = true;
							SS6.ajax({
								loaderElement: null,
								url: url,
								success: onLoadPreview
							});
						}
					},
					out: function () {}
				});
			$previewBox
				.mouseenter(function () {
					keepVisible = true;
				})
				.mouseleave(function() {
					$previewBox.hide();
				});
		};

		var showInWindow = function () {
			SS6.window({
				content: $previewBoxWindow.html(),
				wide: true
			});
		};

		var onLoadPreview = function (responseHtml) {
			isLoading = false;
			isLoaded = true;
			$previewBoxWindow.html(responseHtml);
			$previewBoxWindow.show();
			if (showInWindowAfterLoad) {
				showInWindow();
			}
		};
	};

	$(document).ready(function () {
		$('.js-order-preview').each(function () {
			var orderPreview = new SS6.order.OrderPreview($(this));
			orderPreview.init();
		});
	});

})(jQuery);
