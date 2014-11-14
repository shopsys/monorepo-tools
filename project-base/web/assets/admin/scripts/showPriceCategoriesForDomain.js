(function ($) {

	SS6 = window.SS6 || {};
	SS6.showPriceCategoriesForDomain = SS6.showPriceCategoriesForDomain || {};

	var applyClassSelector = '.js-change-price-categories';
	var optGroupClassPrefix = 'optPriceGroupDomain_';

	$(document).ready(function() {
		SS6.showPriceCategoriesForDomain.init();
	});

	SS6.showPriceCategoriesForDomain.init = function() {
		if ($(applyClassSelector).length !== 0) {
			SS6.showPriceCategoriesForDomain.changeOptgroupLabelsToClass();
			var domainId = $(applyClassSelector + ' input.js-domain-id').data('domain-id');
			if ($(applyClassSelector + ' select').length !== 0) {
				domainId = $(applyClassSelector + ' select').val();
			}
			SS6.showPriceCategoriesForDomain.reloadPriceGroups(domainId);
			$(applyClassSelector + ' select').change(function() {
				SS6.showPriceCategoriesForDomain.reloadPriceGroups($(this).val());
			});
		}
	};

	SS6.showPriceCategoriesForDomain.reloadPriceGroups = function(domain) {
		var $mainContainer = $(applyClassSelector).parent().find('#customer_userData_pricingGroup');
		SS6.showPriceCategoriesForDomain.changeVisibilityOptions($mainContainer.find('option'),false);
		SS6.showPriceCategoriesForDomain.changeVisibilityOptions($mainContainer.find('option.' + optGroupClassPrefix + parseInt(domain)), true);
		SS6.showPriceCategoriesForDomain.changeVisibilityOptions($mainContainer.find('option.' + optGroupClassPrefix + 'all'), true);
		$(applyClassSelector).parent().find('#customer_userData_pricingGroup option').each(function() {
			$(this).attr("selected", false);
		});
	};

	SS6.showPriceCategoriesForDomain.changeVisibilityOptions = function (elements, show) {
		$.map(elements, function(element) {
			SS6.toggleOption($(element), show);
		});
	};

	SS6.showPriceCategoriesForDomain.changeOptgroupLabelsToClass = function () {
		$(applyClassSelector).parent().find('#customer_userData_pricingGroup > option').addClass(optGroupClassPrefix + 'all');
		$(applyClassSelector).parent().find('#customer_userData_pricingGroup optgroup').each(function() {
			var domainId = $(this).attr('label');
			$(this).attr('label','')
				.find('option').each(function() {
					$(this).addClass(optGroupClassPrefix + domainId)
						.parent().parent()
						.append($(this));
				});
			$(this).remove();
		});
	};

})(jQuery);
