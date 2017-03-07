<?php

namespace Tests\ShopBundle\Unit\Form\Admin\AdvancedSearch;

use Shopsys\ShopBundle\Form\Admin\AdvancedSearch\AdvancedSearchOperatorTranslation;
use Shopsys\ShopBundle\Model\AdvancedSearch\ProductAdvancedSearchConfig;
use Tests\ShopBundle\Test\FunctionalTestCase;

class AdvancedSearchOperatorTranslationTest extends FunctionalTestCase
{
    public function testTranslateOperator()
    {
        $productAdvancedSearchConfig = $this->getContainer()->get(ProductAdvancedSearchConfig::class);
        /* @var $productAdvancedSearchConfig \Shopsys\ShopBundle\Model\AdvancedSearch\ProductAdvancedSearchConfig */
        $orderAdvancedSearchConfig = $this->getContainer()->get(ProductAdvancedSearchConfig::class);
        /* @var $orderAdvancedSearchConfig \Shopsys\ShopBundle\Model\AdvancedSearch\OrderAdvancedSearchConfig */

        $advancedSearchOperatorTranslation = $this->getContainer()->get(AdvancedSearchOperatorTranslation::class);
        /* @var $advancedSearchOperatorTranslation \Shopsys\ShopBundle\Form\Admin\AdvancedSearch\AdvancedSearchOperatorTranslation */

        $operators = [];
        foreach ($productAdvancedSearchConfig->getAllFilters() as $filter) {
            $operators = array_merge($operators, $filter->getAllowedOperators());
        }
        foreach ($orderAdvancedSearchConfig->getAllFilters() as $filter) {
            $operators = array_merge($operators, $filter->getAllowedOperators());
        }

        foreach ($operators as $operator) {
            $this->assertNotEmpty($advancedSearchOperatorTranslation->translateOperator($operator));
        }
    }

    public function testTranslateOperatorNotFoundException()
    {
        $advancedSearchTranslator = new AdvancedSearchOperatorTranslation();

        $this->setExpectedException(\Shopsys\ShopBundle\Model\AdvancedSearch\Exception\AdvancedSearchTranslationNotFoundException::class);
        $advancedSearchTranslator->translateOperator('nonexistingOperator');
    }
}
