<?php

declare(strict_types=1);

namespace Tests\ShopBundle\Functional\Form\Admin\AdvancedSearch;

use Shopsys\FrameworkBundle\Form\Admin\AdvancedSearch\AdvancedSearchOperatorTranslation;
use Shopsys\FrameworkBundle\Model\AdvancedSearch\ProductAdvancedSearchConfig;
use Tests\ShopBundle\Test\FunctionalTestCase;

class AdvancedSearchOperatorTranslationTest extends FunctionalTestCase
{
    public function testTranslateOperator()
    {
        /** @var \Shopsys\FrameworkBundle\Model\AdvancedSearch\ProductAdvancedSearchConfig $productAdvancedSearchConfig */
        $productAdvancedSearchConfig = $this->getContainer()->get(ProductAdvancedSearchConfig::class);
        /** @var \Shopsys\FrameworkBundle\Model\AdvancedSearch\OrderAdvancedSearchConfig $orderAdvancedSearchConfig */
        $orderAdvancedSearchConfig = $this->getContainer()->get(ProductAdvancedSearchConfig::class);

        /** @var \Shopsys\FrameworkBundle\Form\Admin\AdvancedSearch\AdvancedSearchOperatorTranslation $advancedSearchOperatorTranslation */
        $advancedSearchOperatorTranslation = $this->getContainer()->get(AdvancedSearchOperatorTranslation::class);

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
