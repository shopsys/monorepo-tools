/**
 * Hybrid tabs component that can work in two modes:
 * - "single" - only one tab can be selected at once (aka. classic tabs)
 * - "multiple" - multiple tabs can be selected at once (aka. accordion)
 *
 * == Notes ==
 * - There can be more "js-tab-button" for each "js-tab-content" but there
 *   always must be at least one "js-tab-button" for each "js-tab-content".
 * - Default open tabs are determined by setting "active" class to certain
 *   "js-tab-button" buttons.
 *
 * == Examples ==
 * === HTML mark-up ===
 *	<div id="container">
 *		<a href="#" class="js-tab-button" data-tab-id="content-a"></a>
 *		<a href="#" class="js-tab-button" data-tab-id="content-b"></a>
 *
 *		<div class="js-tab-content" data-tab-id="content-a"></div>
 *		<div class="js-tab-content" data-tab-id="content-b"></div>
 *	</div>
 *
 * === Single tab mode initialization ===
 *	var hybridTabs = new SS6.hybridTabs.HybridTabs($('#container'));
 *	hybridTabs.init(SS6.hybridTabs.TABS_MODE_SINGLE);
 *
 * === Multiple tabs mode initialization ===
 *	var hybridTabs = new SS6.hybridTabs.HybridTabs($('#container'));
 *	hybridTabs.init(SS6.hybridTabs.TABS_MODE_MULTIPLE);
 */

(function ($) {
	SS6 = window.SS6 || {};
	SS6.hybridTabs = SS6.hybridTabs || {};

	SS6.hybridTabs.TABS_MODE_SINGLE = 'single';
	SS6.hybridTabs.TABS_MODE_MULTIPLE = 'multiple';

	SS6.hybridTabs.HybridTabs = function ($container) {
		var $tabButtons = $container.find('.js-tabs-button');
		var $tabContents = $container.find('.js-tabs-content');
		var tabsMode = null;

		this.init = function (initialTabsMode) {
			tabsMode = initialTabsMode;

			$tabButtons.click(onClickTabButton);

			fixTabsState();
		};

		this.setTabsMode = function (newTabsMode) {
			tabsMode = newTabsMode;
			fixTabsState();
		};

		function fixTabsState() {
			if (tabsMode === SS6.hybridTabs.TABS_MODE_SINGLE) {
				var $lastActiveButton = $tabButtons.filter('.active').last();
				if ($lastActiveButton.length > 0) {
					activateOneTabAndDeactivateOther($lastActiveButton.data('tab-id'));
				} else {
					activateOneTabAndDeactivateOther($tabButtons.first().data('tab-id'));
				}
			}
		}

		function onClickTabButton() {
			var tabId = $(this).data('tab-id');

			if (tabsMode === SS6.hybridTabs.TABS_MODE_SINGLE) {
				activateOneTabAndDeactivateOther(tabId);
			} else if (tabsMode === SS6.hybridTabs.TABS_MODE_MULTIPLE) {
				toggleTab(tabId);
			}

			return false;
		}

		// activates exactely one tab (in "single" mode)
		function activateOneTabAndDeactivateOther(tabId) {
			$tabButtons.each(function () {
				var currentTabId = $(this).data('tab-id');

				if (currentTabId === tabId) {
					activateTab(currentTabId);
				} else {
					deactivateTab(currentTabId);
				}
			});
		}

		// toggles tab (in "multiple" mode)
		function toggleTab(tabId) {
			var $tabButton = $tabButtons.filter('[data-tab-id="' + tabId + '"]');
			var isTabActive = $tabButton.hasClass('active');

			if (isTabActive) {
				deactivateTab(tabId);
			} else {
				activateTab(tabId);
			}
		}

		// activates tab without checking single/multiple mode
		function activateTab(tabId) {
			var $tabButton = $tabButtons.filter('[data-tab-id="' + tabId + '"]');
			var $tabContent = $tabContents.filter('[data-tab-id="' + tabId + '"]');

			$tabButton.addClass('active');
			$tabContent.addClass('active');
			$tabContent.show();
		}

		// deactivates tab without checking single/multiple mode
		function deactivateTab(tabId) {
			var $tabButton = $tabButtons.filter('[data-tab-id="' + tabId + '"]');
			var $tabContent = $tabContents.filter('[data-tab-id="' + tabId + '"]');

			$tabButton.removeClass('active');
			$tabContent.removeClass('active');
			$tabContent.hide();
		}
	};

})(jQuery);