(function ($) {

	SS6 = window.SS6 || {};
	SS6.navigation = SS6.navigation || {};

	SS6.navigation.init = function () {
		$('.js-main-navigation').on('mouseenter', 'a:not(#js-navig-item-logout, .js-navig-item-active)', updateNavigationInfo);
		$('.js-main-navigation').on('mouseleave', 'a:not(#js-navig-item-logout)', updateNavigationInfo);
	};

	var navigationTypes = Object.freeze({
		ACTUAL: 'actual',
		GOTO: 'goto'
	});

	var updateNavigationInfo = function (event) {
		switch (event.type) {
			case 'mouseenter':
				$('#js-position-go-to').text($(this).attr('title'));
				showNavigation(navigationTypes.GOTO);
				break;
			case 'mouseleave':
				showNavigation(navigationTypes.ACTUAL);
				break;
		}
	};

	var showNavigation = function (navigationType) {
		$('#js-position-actual-container').toggle(navigationType === navigationTypes.ACTUAL);
		$('#js-position-go-to-container').toggle(navigationType === navigationTypes.GOTO);
	};

	$(document).ready(function () {
		SS6.navigation.init();
	});

})(jQuery);