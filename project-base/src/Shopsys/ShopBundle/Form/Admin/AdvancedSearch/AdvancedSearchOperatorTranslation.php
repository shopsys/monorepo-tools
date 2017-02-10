<?php

namespace Shopsys\ShopBundle\Form\Admin\AdvancedSearch;

use Shopsys\ShopBundle\Model\AdvancedSearch\AdvancedSearchFilterInterface;

class AdvancedSearchOperatorTranslation
{

    /**
     * @var string[operator]
     */
    private $operatorsTranslations;

    public function __construct() {
        $this->operatorsTranslations = [
            AdvancedSearchFilterInterface::OPERATOR_CONTAINS => t('include'),
            AdvancedSearchFilterInterface::OPERATOR_NOT_CONTAINS => t('doesn\'t include'),
            AdvancedSearchFilterInterface::OPERATOR_NOT_SET => t('not entered'),
            AdvancedSearchFilterInterface::OPERATOR_IS => t('is'),
            AdvancedSearchFilterInterface::OPERATOR_IS_NOT => t('not'),
            AdvancedSearchFilterInterface::OPERATOR_IS_USED => t('uses'),
            AdvancedSearchFilterInterface::OPERATOR_IS_NOT_USED => t('doesn\'t use'),
            AdvancedSearchFilterInterface::OPERATOR_BEFORE => t('before'),
            AdvancedSearchFilterInterface::OPERATOR_AFTER => t('after'),
            AdvancedSearchFilterInterface::OPERATOR_GT => t('higher than'),
            AdvancedSearchFilterInterface::OPERATOR_LT => t('lower than'),
            AdvancedSearchFilterInterface::OPERATOR_GTE => t('higher or equal'),
            AdvancedSearchFilterInterface::OPERATOR_LTE => t('lower or equal'),
        ];
    }

    /**
     * @param string $operator
     * @return string
     */
    public function translateOperator($operator) {
        if (array_key_exists($operator, $this->operatorsTranslations)) {
            return $this->operatorsTranslations[$operator];
        }

        $message = 'Operator "' . $operator . '" translation not found.';
        throw new \Shopsys\ShopBundle\Model\AdvancedSearch\Exception\AdvancedSearchTranslationNotFoundException($message);
    }

}
