<?php

namespace Tests\ShopBundle\Unit\Form\Admin\AdvancedSearch;

use Shopsys\FrameworkBundle\Form\Admin\AdvancedSearch\AdvancedSearchOrderFilterTranslation;
use Shopsys\FrameworkBundle\Model\AdvancedSearch\OrderAdvancedSearchConfig;
use Tests\ShopBundle\Test\FunctionalTestCase;

class AdvancedSearchOrderFilterTranslationTest extends FunctionalTestCase
{
    public function testTranslateFilterName()
    {
        $advancedSearchConfig = $this->getServiceByType(OrderAdvancedSearchConfig::class);
        /* @var $advancedSearchConfig \Shopsys\FrameworkBundle\Model\AdvancedSearch\OrderAdvancedSearchConfig */
        $advancedSearchOrderFilterTranslation = $this->getServiceByType(AdvancedSearchOrderFilterTranslation::class);
        // @codingStandardsIgnoreStart
        /* @var $advancedSearchOrderFilterTranslation \Shopsys\FrameworkBundle\Form\Admin\AdvancedSearch\AdvancedSearchOrderFilterTranslation */
        // @codingStandardsIgnoreEnd

        foreach ($advancedSearchConfig->getAllFilters() as $filter) {
            $this->assertNotEmpty($advancedSearchOrderFilterTranslation->translateFilterName($filter->getName()));
        }
    }

    public function testTranslateFilterNameNotFoundException()
    {
        $advancedSearchTranslator = new AdvancedSearchOrderFilterTranslation();

        $this->expectException(\Shopsys\FrameworkBundle\Model\AdvancedSearch\Exception\AdvancedSearchTranslationNotFoundException::class);
        $advancedSearchTranslator->translateFilterName('nonexistingFilterName');
    }
}
