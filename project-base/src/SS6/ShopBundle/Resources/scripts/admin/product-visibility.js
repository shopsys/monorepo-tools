(function ($) {

	SS6 = window.SS6 || {};
	SS6.product = SS6.product || {};

	SS6.product.ProductVisibility = function ($productVisibility) {
		var $visibilityIcon = $productVisibility.find('.js-product-visibility-icon');
		var $visibilityBox = $productVisibility.find('.js-product-visibility-box');
		var $visibilityBoxWindow = $visibilityBox.find('.js-product-visibility-box-window');

		var url = $productVisibility.data('visibility-url');
		var isLoading = false;
		var isLoaded = false;
		var showInWindowAfterLoad = false;

		this.init = function () {
			$visibilityIcon
				.mouseenter(function () {
					$visibilityBox.show();
				})
				.mouseleave(function () {
					$visibilityBox.hide();
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
								success: onLoadVisibility
							});
						}
					},
					out: function () {}
				});
		};

		var showInWindow = function () {
			SS6.window({
				content: $visibilityBoxWindow.html()
			});
		};

		var onLoadVisibility = function (responseHtml) {
			isLoading = false;
			isLoaded = true;
			$visibilityBoxWindow.html(responseHtml);
			$visibilityBoxWindow.show();
			if (showInWindowAfterLoad) {
				showInWindow();
			}
		};
	};

	$(document).ready(function () {
		$('.js-product-visibility').each(function () {
			var productVisibility = new SS6.product.ProductVisibility($(this));
			productVisibility.init();
		});
	});

})(jQuery);