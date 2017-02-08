<?php

namespace Shopsys\ShopBundle\Tests\Unit\Form\Admin\AdvancedSearch;

use Shopsys\ShopBundle\Form\Admin\AdvancedSearch\AdvancedSearchProductFilterTranslation;
use Shopsys\ShopBundle\Model\AdvancedSearch\ProductAdvancedSearchConfig;
use Shopsys\ShopBundle\Tests\Test\FunctionalTestCase;

class AdvancedSearchProductFilterTranslationTest extends FunctionalTestCase {

	public function testTranslateFilterName() {
		$advancedSearchConfig = $this->getContainer()->get(ProductAdvancedSearchConfig::class);
		/* @var $advancedSearchConfig \Shopsys\ShopBundle\Model\AdvancedSearch\ProductAdvancedSearchConfig */
		$advancedSearchProductFilterTranslation = $this->getContainer()->get(AdvancedSearchProductFilterTranslation::class);
		// @codingStandardsIgnoreStart
		/* @var $advancedSearchProductFilterTranslation \Shopsys\ShopBundle\Form\Admin\AdvancedSearch\AdvancedSearchProductFilterTranslation */
		// @codingStandardsIgnoreEnd

		foreach ($advancedSearchConfig->getAllFilters() as $filter) {
			$this->assertNotEmpty($advancedSearchProductFilterTranslation->translateFilterName($filter->getName()));
		}
	}

	public function testTranslateFilterNameNotFoundException() {
		$advancedSearchTranslator = new AdvancedSearchProductFilterTranslation();

		$this->setExpectedException(\Shopsys\ShopBundle\Model\AdvancedSearch\Exception\AdvancedSearchTranslationNotFoundException::class);
		$advancedSearchTranslator->translateFilterName('nonexistingFilterName');
	}

}
