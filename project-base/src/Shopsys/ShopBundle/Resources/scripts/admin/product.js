(function ($) {

	SS6 = window.SS6 || {};
	SS6.product = SS6.product || {};

	SS6.product.init = function () {
		var usingStockSelection = $('#product_edit_form_productData_usingStock input[type="radio"]');
		var $outOfStockActionSelection = $('select[name="product_edit_form[productData][outOfStockAction]"]');

		usingStockSelection.change(function () {
			SS6.product.toggleIsUsingStock($(this).val() === '1');
		});

		$outOfStockActionSelection.change(function () {
			SS6.product.toggleIsUsingAlternateAvailability($(this).val() === SS6.constant('\\Shopsys\\ShopBundle\\Model\\Product\\Product::OUT_OF_STOCK_ACTION_SET_ALTERNATE_AVAILABILITY'));
		});

		SS6.product.toggleIsUsingStock(usingStockSelection.filter(':checked').val() === '1');
		SS6.product.toggleIsUsingAlternateAvailability($outOfStockActionSelection.val() === SS6.constant('\\Shopsys\\ShopBundle\\Model\\Product\\Product::OUT_OF_STOCK_ACTION_SET_ALTERNATE_AVAILABILITY'));

		SS6.product.initializeSideNavigation();

		$('#js-close-without-saving').on('click', function () {
			window.close();
			return false;
		});
	};

	SS6.product.toggleIsUsingStock = function (isUsingStock) {
		$('.js-product-using-stock').toggle(isUsingStock);
		$('.js-product-not-using-stock').toggle(!isUsingStock);
	};

	SS6.product.toggleIsUsingAlternateAvailability = function (isUsingStockAndAlternateAvailability) {
		$('.js-product-using-stock-and-alternate-availability').toggle(isUsingStockAndAlternateAvailability);
	};

	SS6.product.initializeSideNavigation = function () {
		var $productDetailNavigation = $('.js-product-detail-navigation');
		var $webContent = $('.web__content');

		$('.form-group__title, .form-full__title').each(function () {
			var $title = $(this);
			var $titleClone = $title.clone();

			$titleClone.find('.js-validation-errors-list').remove();
			var $navigationItem = $('<li class="anchor-menu__item"><span class="anchor-menu__item__anchor link cursor-pointer">' + $titleClone.text() + '</span></li>');
			$productDetailNavigation.append($navigationItem);

			$navigationItem.click(function () {
				var scrollOffsetTop = $title.offset().top - $webContent.offset().top
				$('html, body').animate({ scrollTop: scrollOffsetTop }, 'slow');
			});
		});
	};

	$(document).ready(function () {
		SS6.product.init();
	});

})(jQuery);
