(function ($) {

	SS6 = window.SS6 || {};
	SS6.productList = SS6.productList || {};

	SS6.register.registerCallback(function () {
		$('.js-product-list-ordering-mode').click(function () {
			var cookieName = $(this).data('cookie-name');
			var orderingName = $(this).data('ordering-mode');

			$.cookie(cookieName, orderingName, { path: '/' });
			location.reload(true);

			return false;
		});
	});

	$(document).ready(function () {
		var ajaxMoreLoader = new SS6.productList.AjaxMoreLoader();
		ajaxMoreLoader.init();
		var ajaxFilter = new SS6.productList.AjaxFilter(ajaxMoreLoader);
		ajaxFilter.init();
	});

})(jQuery);
