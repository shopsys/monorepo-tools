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
			var keepVisible = false;
			$visibilityIcon
				.mouseleave(function () {
					keepVisible = false;
					setTimeout(function () {
						if (!keepVisible) {
							$visibilityBox.hide();
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
						$visibilityBox.show();
						if (!isLoaded && !isLoading) {
							isLoading = true;
							SS6.ajax({
								loaderElement: null,
								url: url,
								success: onLoadVisibility
							});
						}
					},
					out: function () {}
				});
			$visibilityBox
				.mouseenter(function () {
					keepVisible = true;
				})
				.mouseleave(function() {
					$visibilityBox.hide();
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