(function ($) {

	SS6 = window.SS6 || {};
	SS6.productList = SS6.productList || {};

	SS6.register.registerCallback(function () {
		$('.js-product-list-ordering-mode').change(function () {
			var cookieName = $(this).data('cookie-name');

			$.cookie(cookieName, $(this).val(), { path: '/' });

			location.reload(true);
		});
	});

	$(document).ready(function () {
		var ajaxMoreLoader = new SS6.productList.AjaxMoreLoader();
		ajaxMoreLoader.init();
		var ajaxFilter = new SS6.productList.AjaxFilter(ajaxMoreLoader);
		ajaxFilter.init();
	});

})(jQuery);
