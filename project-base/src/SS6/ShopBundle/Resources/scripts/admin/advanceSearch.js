(function ($) {

	SS6 = window.SS6 || {};
	SS6.advanceSearch = SS6.advanceSearch || {};

	SS6.advanceSearch.init = function () {
		var $quickSearch = $('#js-quick-search');
		var $advanceSearch = $('#js-advance-search');
		var $addRuleButton = $('#js-advance-search-add-rule-button');
		var $enableButton = $('#js-advance-search-enable-button');
		var $rulesContainer = $('#js-advance-search-rules-container');
		var $ruleTemplate = $('#js-advance-search-rule-template');
		$ruleTemplate.detach().show().removeAttr('id').find('*[id]').removeAttr('id');

		var newRuleIndexCounter = 0;

		SS6.advanceSearch.registerEnableButton($enableButton, $quickSearch, $advanceSearch);
		SS6.advanceSearch.actualizeAllValuesByOperator($rulesContainer);

		$addRuleButton.click(function () {
			SS6.advanceSearch.addRule($rulesContainer, $ruleTemplate, 'new_' + newRuleIndexCounter);
			newRuleIndexCounter++;
			return false;
		});

		$rulesContainer.on('click', '.js-advance-search-remove-rule-button', function () {
			$(this).closest('.js-advance-search-rule').remove();
			return false;
		});

		$rulesContainer.on('change', '.js-advance-search-rule-subject', function () {
			var $rule = $(this).closest('.js-advance-search-rule');
			SS6.advanceSearch.actualizeRule($rulesContainer, $rule, $(this).val());
		});

		$rulesContainer.on('change', '.js-advance-search-rule-operator', function () {
			var $rule = $(this).closest('.js-advance-search-rule');
			SS6.advanceSearch.actualizeValueByOperator($rulesContainer, $rule, $(this).val());
		});
	};

	SS6.advanceSearch.registerEnableButton = function ($enableButton, $quickSearch, $advanceSearch) {
		if ($enableButton.size() > 0) {
			$advanceSearch.detach();
			$enableButton.click(function () {
				$quickSearch.replaceWith($advanceSearch.show());
				return false;
			});
		}
	}

	SS6.advanceSearch.actualizeRule = function ($rulesContainer, $rule, filterName) {
		$rule.addClass('advance-search-rule-disabled');
		$.ajax({
			url: $rulesContainer.data('rule-form-url'),
			type: 'post',
			data: {filterName: filterName},
			success: function(data) {
				var $newRule = $($.parseHTML(data));
				$rule.replaceWith($newRule);
			}
		});
	};

	SS6.advanceSearch.addRule = function ($rulesContainer, $ruleTemplate, newIndex) {
		var ruleHtml = $ruleTemplate.clone().wrap('<div>').parent().html().replace(/__template__/g, newIndex);
		var $rule = $($.parseHTML(ruleHtml));
		$rule.appendTo($rulesContainer);
	};

	SS6.advanceSearch.actualizeAllValuesByOperator = function ($rulesContainer) {
		$rulesContainer.find('.js-advance-search-rule').each(function () {
			var operator = $(this).find('.js-advance-search-rule-operator').val();
			SS6.advanceSearch.actualizeValueByOperator($rulesContainer, $(this), operator);
		});
	};

	SS6.advanceSearch.actualizeValueByOperator = function ($rulesContainer, $rule, operator) {
		$rule.find('.js-advance-search-rule-value').toggle(operator !== 'notSet');
	};

	$(document).ready(function () {
		SS6.advanceSearch.init();
	});

})(jQuery);
