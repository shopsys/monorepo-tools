<?php

declare(strict_types=1);

namespace Tests\ShopBundle\Functional\Form\Admin\AdvancedSearch;

use Shopsys\FrameworkBundle\Form\Admin\AdvancedSearch\AdvancedSearchProductFilterTranslation;
use Shopsys\FrameworkBundle\Model\AdvancedSearch\ProductAdvancedSearchConfig;
use Tests\ShopBundle\Test\FunctionalTestCase;

class AdvancedSearchProductFilterTranslationTest extends FunctionalTestCase
{
    public function testTranslateFilterName()
    {
        /** @var \Shopsys\FrameworkBundle\Model\AdvancedSearch\ProductAdvancedSearchConfig $advancedSearchConfig */
        $advancedSearchConfig = $this->getContainer()->get(ProductAdvancedSearchConfig::class);
        /** @var \Shopsys\FrameworkBundle\Form\Admin\AdvancedSearch\AdvancedSearchProductFilterTranslation $advancedSearchProductFilterTranslation */
        $advancedSearchProductFilterTranslation = $this->getContainer()->get(AdvancedSearchProductFilterTranslation::class);

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
