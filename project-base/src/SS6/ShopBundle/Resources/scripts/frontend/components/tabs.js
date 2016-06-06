/**
 * Classic tabs component that uses HybridTabs component in single tab mode.
 *
 * @see SS6.HybridTabs
 *
 * == Notes ==
 * - There must be at least one "js-tab-button" for each "js-tab-content".
 * - Default open tab is determined by setting "active" class to a certain
 *   "js-tab-button" button.
 *
 * == Example ==
 * === HTML mark-up ===
 *	<div class="js-tabs">
 *		<a href="#" class="js-tab-button" data-tab-id="content-a"></a>
 *		<a href="#" class="js-tab-button" data-tab-id="content-b"></a>
 *
 *		<div class="js-tab-content" data-tab-id="content-a"></div>
 *		<div class="js-tab-content" data-tab-id="content-b"></div>
 *	</div>
 *
 * === JavaScript ===
 * There is no need to initialize the component in JavaScript.
 * It is automatically initialized on all DOM containers with class "js-tabs".
 */

(function ($) {
	SS6 = window.SS6 || {};

	$(document).ready(function () {
		$('.js-tabs').each(function () {
			var hybridTabs = new SS6.hybridTabs.HybridTabs($(this));
			hybridTabs.init(SS6.hybridTabs.TABS_MODE_SINGLE);
		});
	});

})(jQuery);