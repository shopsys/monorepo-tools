<?php

declare(strict_types=1);

namespace Tests\ShopBundle\Functional\Form\Admin\AdvancedSearch;

use Shopsys\FrameworkBundle\Form\Admin\AdvancedSearch\AdvancedSearchOrderFilterTranslation;
use Shopsys\FrameworkBundle\Model\AdvancedSearch\OrderAdvancedSearchConfig;
use Tests\ShopBundle\Test\FunctionalTestCase;

class AdvancedSearchOrderFilterTranslationTest extends FunctionalTestCase
{
    public function testTranslateFilterName()
    {
        /** @var \Shopsys\FrameworkBundle\Model\AdvancedSearch\OrderAdvancedSearchConfig $advancedSearchConfig */
        $advancedSearchConfig = $this->getContainer()->get(OrderAdvancedSearchConfig::class);
        /** @var \Shopsys\FrameworkBundle\Form\Admin\AdvancedSearch\AdvancedSearchOrderFilterTranslation $advancedSearchOrderFilterTranslation */
        $advancedSearchOrderFilterTranslation = $this->getContainer()->get(AdvancedSearchOrderFilterTranslation::class);

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
