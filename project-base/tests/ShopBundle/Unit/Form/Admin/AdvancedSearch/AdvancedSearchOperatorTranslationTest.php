<?php

namespace Tests\ShopBundle\Unit\Form\Admin\AdvancedSearch;

use Shopsys\FrameworkBundle\Form\Admin\AdvancedSearch\AdvancedSearchOperatorTranslation;
use Shopsys\FrameworkBundle\Model\AdvancedSearch\ProductAdvancedSearchConfig;
use Tests\ShopBundle\Test\FunctionalTestCase;

class AdvancedSearchOperatorTranslationTest extends FunctionalTestCase
{
    public function testTranslateOperator()
    {
        $productAdvancedSearchConfig = $this->getServiceByType(ProductAdvancedSearchConfig::class);
        /* @var $productAdvancedSearchConfig \Shopsys\FrameworkBundle\Model\AdvancedSearch\ProductAdvancedSearchConfig */
        $orderAdvancedSearchConfig = $this->getServiceByType(ProductAdvancedSearchConfig::class);
        /* @var $orderAdvancedSearchConfig \Shopsys\FrameworkBundle\Model\AdvancedSearch\OrderAdvancedSearchConfig */

        $advancedSearchOperatorTranslation = $this->getServiceByType(AdvancedSearchOperatorTranslation::class);
        /* @var $advancedSearchOperatorTranslation \Shopsys\FrameworkBundle\Form\Admin\AdvancedSearch\AdvancedSearchOperatorTranslation */

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

        $this->expectException(\Shopsys\FrameworkBundle\Model\AdvancedSearch\Exception\AdvancedSearchTranslationNotFoundException::class);
        $advancedSearchTranslator->translateOperator('nonexistingOperator');
    }
}
