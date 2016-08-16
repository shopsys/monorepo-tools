(function ($) {

	SS6 = window.SS6 || {};
	SS6.tabs = SS6.tabs || {};

	SS6.tabs.Tabs = function ($tabs) {
		var self = this;

		var $tabLinks = $tabs.find('.js-tabs-tab-link');
		var $activeTabLinks = $tabLinks.filter('.active');
		var $tabContents = $tabs.find('.js-tabs-tab-content');

		this.init = function () {
			$tabLinks.bind('click.selectTab', onSelectTab);
			if ($activeTabLinks.length === 0) {
				$tabLinks.first().addClass('active');
			}
			$activeTabLinks = $tabLinks.filter('.active');
			$activeTabLinks.trigger('click.selectTab');
		};

		var onSelectTab = function () {
			$tabLinks.removeClass('active');
			$tabLinks.filter('[data-tab-id="' + $(this).data('tab-id') + '"]').addClass('active');
			$tabContents.hide();
			$tabContents.filter('[data-tab-id="' + $(this).data('tab-id') + '"]').show();

			return false;
		};

	};

	SS6.register.registerCallback(function ($container) {
		$container.filterAllNodes('.js-tabs').each(function () {
			var tab = new SS6.tabs.Tabs($(this));
			tab.init();
		});
	});

})(jQuery);