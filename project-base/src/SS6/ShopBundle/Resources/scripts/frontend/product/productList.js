(function ($) {

	SS6 = window.SS6 || {};
	SS6.productList = SS6.productList || {};

	SS6.productList.init = function () {
		$('.js-productListOrderingMode').change(function () {
			var cookieName = $(this).data('cookie-name');

			$.cookie(cookieName, $(this).val(), { path: '/' });

			location.reload(true);
		});
	};

	$(document).ready(function () {
		SS6.productList.init();
	});

})(jQuery);
