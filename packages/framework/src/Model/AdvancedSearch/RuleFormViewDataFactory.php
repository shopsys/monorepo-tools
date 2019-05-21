<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Model\AdvancedSearch;

class RuleFormViewDataFactory
{
    public const TEMPLATE_RULE_FORM_KEY = '__template__';

    /**
     * @param string $defaultFilterName
     * @param array|null $requestData
     * @return array
     */
    public function createFromRequestData(string $defaultFilterName, ?array $requestData = null): array
    {
        if ($requestData === null) {
            $searchRulesViewData = [];
        } else {
            $searchRulesViewData = array_values($requestData);
        }

        if (count($searchRulesViewData) === 0) {
            $searchRulesViewData[] = $this->createDefault($defaultFilterName);
        }

        $searchRulesViewData[self::TEMPLATE_RULE_FORM_KEY] = $this->createDefault($defaultFilterName);

        return $searchRulesViewData;
    }

    /**
     * @param string $filterName
     * @return array
     */
    public function createDefault(string $filterName): array
    {
        return [
            'subject' => $filterName,
            'operator' => null,
            'value' => null,
        ];
    }
}
