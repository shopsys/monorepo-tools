<?php

namespace Tests\ShopBundle\Unit\Form\Admin\AdvancedSearch;

use Shopsys\ShopBundle\Form\Admin\AdvancedSearch\AdvancedSearchProductFilterTranslation;
use Shopsys\ShopBundle\Model\AdvancedSearch\ProductAdvancedSearchConfig;
use Tests\ShopBundle\Test\FunctionalTestCase;

class AdvancedSearchProductFilterTranslationTest extends FunctionalTestCase
{
    public function testTranslateFilterName()
    {
        $advancedSearchConfig = $this->getServiceByType(ProductAdvancedSearchConfig::class);
        /* @var $advancedSearchConfig \Shopsys\ShopBundle\Model\AdvancedSearch\ProductAdvancedSearchConfig */
        $advancedSearchProductFilterTranslation = $this->getServiceByType(AdvancedSearchProductFilterTranslation::class);
        // @codingStandardsIgnoreStart
        /* @var $advancedSearchProductFilterTranslation \Shopsys\ShopBundle\Form\Admin\AdvancedSearch\AdvancedSearchProductFilterTranslation */
        // @codingStandardsIgnoreEnd

        foreach ($advancedSearchConfig->getAllFilters() as $filter) {
            $this->assertNotEmpty($advancedSearchProductFilterTranslation->translateFilterName($filter->getName()));
        }
    }

    public function testTranslateFilterNameNotFoundException()
    {
        $advancedSearchTranslator = new AdvancedSearchProductFilterTranslation();

        $this->expectException(\Shopsys\ShopBundle\Model\AdvancedSearch\Exception\AdvancedSearchTranslationNotFoundException::class);
        $advancedSearchTranslator->translateFilterName('nonexistingFilterName');
    }
}
