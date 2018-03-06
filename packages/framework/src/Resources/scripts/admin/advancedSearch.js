(function ($) {

    Shopsys = window.Shopsys || {};
    Shopsys.advancedSearch = Shopsys.advancedSearch || {};

    Shopsys.advancedSearch.init = function ($addRuleButton, $rulesContainer, $ruleTemplate) {
        $ruleTemplate.detach().removeClass('display-none').removeAttr('id').find('*[id]').removeAttr('id');

        var newRuleIndexCounter = 0;

        Shopsys.advancedSearch.updateAllValuesByOperator($rulesContainer);

        $addRuleButton.click(function () {
            Shopsys.advancedSearch.addRule($rulesContainer, $ruleTemplate, 'new_' + newRuleIndexCounter);
            newRuleIndexCounter++;
            return false;
        });

        $rulesContainer.on('click', '.js-advanced-search-remove-rule-button', function () {
            $(this).closest('.js-advanced-search-rule').remove();
            return false;
        });

        $rulesContainer.on('change', '.js-advanced-search-rule-subject', function () {
            var $rule = $(this).closest('.js-advanced-search-rule');
            Shopsys.advancedSearch.updateRule($rulesContainer, $rule, $(this).val(), 'new_' + newRuleIndexCounter);
            newRuleIndexCounter++;
        });

        $rulesContainer.on('change', '.js-advanced-search-rule-operator', function () {
            var $rule = $(this).closest('.js-advanced-search-rule');
            Shopsys.advancedSearch.updateValueByOperator($rulesContainer, $rule, $(this).val());
        });
    };

    Shopsys.advancedSearch.updateRule = function ($rulesContainer, $rule, filterName, newIndex) {
        $rule.addClass('in-disabled');
        Shopsys.ajax({
            loaderElement: '#js-advanced-search-rules-box',
            url: $rulesContainer.data('rule-form-url'),
            type: 'post',
            data: {
                filterName: filterName,
                newIndex: newIndex
            },
            success: function (data) {
                var $newRule = $($.parseHTML(data));
                $rule.replaceWith($newRule);

                Shopsys.register.registerNewContent($newRule);
            }
        });
    };

    Shopsys.advancedSearch.addRule = function ($rulesContainer, $ruleTemplate, newIndex) {
        var ruleHtml = $ruleTemplate.clone().wrap('<div>').parent().html().replace(/__template__/g, newIndex);
        var $rule = $($.parseHTML(ruleHtml));
        $rule.appendTo($rulesContainer);

        Shopsys.register.registerNewContent($rule);
    };

    Shopsys.advancedSearch.updateAllValuesByOperator = function ($rulesContainer) {
        $rulesContainer.find('.js-advanced-search-rule').each(function () {
            var operator = $(this).find('.js-advanced-search-rule-operator').val();
            Shopsys.advancedSearch.updateValueByOperator($rulesContainer, $(this), operator);
        });
    };

    Shopsys.advancedSearch.updateValueByOperator = function ($rulesContainer, $rule, operator) {
        $rule.find('.js-advanced-search-rule-value').toggle(operator !== Shopsys.constant('\\Shopsys\\FrameworkBundle\\Model\\AdvancedSearch\\AdvancedSearchFilterInterface::OPERATOR_NOT_SET'));
    };

    Shopsys.register.registerCallback(function ($container) {
        var $addRuleButton = $container.filterAllNodes('#js-advanced-search-add-rule-button');
        var $rulesContainer = $container.filterAllNodes('#js-advanced-search-rules-container');
        var $ruleTemplate = $container.filterAllNodes('#js-advanced-search-rule-template');

        if ($addRuleButton.length > 0 && $rulesContainer.length > 0 && $ruleTemplate.length > 0) {
            Shopsys.advancedSearch.init($addRuleButton, $rulesContainer, $ruleTemplate);
        }
    });

})(jQuery);
