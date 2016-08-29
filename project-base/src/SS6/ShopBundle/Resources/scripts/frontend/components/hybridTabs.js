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
				var $activeButtons = $tabButtons.filter('.active');
				if ($activeButtons.length > 0) {
					activateOneTabAndDeactivateOther($activeButtons.last().data('tab-id'));
				} else {
					activateOneTabAndDeactivateOther($tabButtons.first().data('tab-id'));
				}
			} else if (tabsMode === SS6.hybridTabs.TABS_MODE_MULTIPLE) {
				// activate all tabs that have at least one active button
				var $activeButtons = $tabButtons.filter('.active');
				$activeButtons.each(function () {
					activateTab($(this).data('tab-id'));
				});

				// deactivate all tabs that have any inactive button left
				// (all the tabs that had any active button have now all the buttons
				// in active state from the previous step)
				var $inactiveButtons = $tabButtons.filter(':not(.active)');
				$inactiveButtons.each(function () {
					deactivateTab($(this).data('tab-id'));
				});
			}
		}

		function onClickTabButton() {
			var tabId = $(this).data('tab-id');

			if (tabsMode === SS6.hybridTabs.TABS_MODE_SINGLE) {
				activateOneTabAndDeactivateOther(tabId);
			} else if (tabsMode === SS6.hybridTabs.TABS_MODE_MULTIPLE) {
				var isTabActive = $(this).hasClass('active');

				if (isTabActive) {
					deactivateTab(tabId);
				} else {
					activateTab(tabId);
				}
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