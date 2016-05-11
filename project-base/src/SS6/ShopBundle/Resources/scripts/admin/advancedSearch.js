(function ($) {

	SS6 = window.SS6 || {};
	SS6.advancedSearch = SS6.advancedSearch || {};

	SS6.advancedSearch.init = function () {
		var $addRuleButton = $('#js-advanced-search-add-rule-button');
		var $rulesContainer = $('#js-advanced-search-rules-container');
		var $ruleTemplate = $('#js-advanced-search-rule-template');
		$ruleTemplate.detach().removeClass('h-display-none').removeAttr('id').find('*[id]').removeAttr('id');

		var newRuleIndexCounter = 0;

		SS6.advancedSearch.actualizeAllValuesByOperator($rulesContainer);

		$addRuleButton.click(function () {
			SS6.advancedSearch.addRule($rulesContainer, $ruleTemplate, 'new_' + newRuleIndexCounter);
			newRuleIndexCounter++;
			return false;
		});

		$rulesContainer.on('click', '.js-advanced-search-remove-rule-button', function () {
			$(this).closest('.js-advanced-search-rule').remove();
			return false;
		});

		$rulesContainer.on('change', '.js-advanced-search-rule-subject', function () {
			var $rule = $(this).closest('.js-advanced-search-rule');
			SS6.advancedSearch.actualizeRule($rulesContainer, $rule, $(this).val(), 'new_' + newRuleIndexCounter);
			newRuleIndexCounter++;
		});

		$rulesContainer.on('change', '.js-advanced-search-rule-operator', function () {
			var $rule = $(this).closest('.js-advanced-search-rule');
			SS6.advancedSearch.actualizeValueByOperator($rulesContainer, $rule, $(this).val());
		});
	};

	SS6.advancedSearch.actualizeRule = function ($rulesContainer, $rule, filterName, newIndex) {
		$rule.addClass('in-disabled');
		SS6.ajax({
			loaderElement: '#js-advanced-search-rules-box',
			url: $rulesContainer.data('rule-form-url'),
			type: 'post',
			data: {
				filterName: filterName,
				newIndex: newIndex
			},
			success: function(data) {
				var $newRule = $($.parseHTML(data));
				$rule.replaceWith($newRule);

				SS6.register.registerNewContent($newRule);
			}
		});
	};

	SS6.advancedSearch.addRule = function ($rulesContainer, $ruleTemplate, newIndex) {
		var ruleHtml = $ruleTemplate.clone().wrap('<div>').parent().html().replace(/__template__/g, newIndex);
		var $rule = $($.parseHTML(ruleHtml));
		$rule.appendTo($rulesContainer);

		SS6.register.registerNewContent($rule);
	};

	SS6.advancedSearch.actualizeAllValuesByOperator = function ($rulesContainer) {
		$rulesContainer.find('.js-advanced-search-rule').each(function () {
			var operator = $(this).find('.js-advanced-search-rule-operator').val();
			SS6.advancedSearch.actualizeValueByOperator($rulesContainer, $(this), operator);
		});
	};

	SS6.advancedSearch.actualizeValueByOperator = function ($rulesContainer, $rule, operator) {
		$rule.find('.js-advanced-search-rule-value').toggle(operator !== SS6.constant('\\SS6\\ShopBundle\\Model\\AdvancedSearch\\AdvancedSearchFilterInterface::OPERATOR_NOT_SET'));
	};

	$(document).ready(function () {
		SS6.advancedSearch.init();
	});

})(jQuery);
