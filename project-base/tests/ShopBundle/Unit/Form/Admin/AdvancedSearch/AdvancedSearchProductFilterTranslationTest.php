<?php

namespace Tests\ShopBundle\Unit\Form\Admin\AdvancedSearch;

use Shopsys\FrameworkBundle\Form\Admin\AdvancedSearch\AdvancedSearchProductFilterTranslation;
use Shopsys\FrameworkBundle\Model\AdvancedSearch\ProductAdvancedSearchConfig;
use Tests\ShopBundle\Test\FunctionalTestCase;

class AdvancedSearchProductFilterTranslationTest extends FunctionalTestCase
{
    public function testTranslateFilterName()
    {
        $advancedSearchConfig = $this->getServiceByType(ProductAdvancedSearchConfig::class);
        /* @var $advancedSearchConfig \Shopsys\FrameworkBundle\Model\AdvancedSearch\ProductAdvancedSearchConfig */
        $advancedSearchProductFilterTranslation = $this->getServiceByType(AdvancedSearchProductFilterTranslation::class);
        // @codingStandardsIgnoreStart
        /* @var $advancedSearchProductFilterTranslation \Shopsys\FrameworkBundle\Form\Admin\AdvancedSearch\AdvancedSearchProductFilterTranslation */
        // @codingStandardsIgnoreEnd

        foreach ($advancedSearchConfig->getAllFilters() as $filter) {
            $this->assertNotEmpty($advancedSearchProductFilterTranslation->translateFilterName($filter->getName()));
        }
    }

    public function testTranslateFilterNameNotFoundException()
    {
        $advancedSearchTranslator = new AdvancedSearchProductFilterTranslation();

        $this->expectException(\Shopsys\FrameworkBundle\Model\AdvancedSearch\Exception\AdvancedSearchTranslationNotFoundException::class);
        $advancedSearchTranslator->translateFilterName('nonexistingFilterName');
    }
}
