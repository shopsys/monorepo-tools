<?php

namespace SS6\ShopBundle\Tests\Unit\Form\Admin\AdvancedSearch;

use SS6\ShopBundle\Form\Admin\AdvancedSearch\AdvancedSearchProductFilterTranslation;
use SS6\ShopBundle\Model\AdvancedSearch\ProductAdvancedSearchConfig;
use SS6\ShopBundle\Tests\Test\FunctionalTestCase;

class AdvancedSearchProductFilterTranslationTest extends FunctionalTestCase {

	public function testTranslateFilterName() {
		$advancedSearchConfig = $this->getContainer()->get(ProductAdvancedSearchConfig::class);
		/* @var $advancedSearchConfig \SS6\ShopBundle\Model\AdvancedSearch\ProductAdvancedSearchConfig */
		$advancedSearchProductFilterTranslation = $this->getContainer()->get(AdvancedSearchProductFilterTranslation::class);
		// @codingStandardsIgnoreStart
		/* @var $advancedSearchProductFilterTranslation \SS6\ShopBundle\Form\Admin\AdvancedSearch\AdvancedSearchProductFilterTranslation */
		// @codingStandardsIgnoreEnd

		foreach ($advancedSearchConfig->getAllFilters() as $filter) {
			$this->assertNotEmpty($advancedSearchProductFilterTranslation->translateFilterName($filter->getName()));
		}
	}

	public function testTranslateFilterNameNotFoundException() {
		$advancedSearchTranslator = new AdvancedSearchProductFilterTranslation();

		$this->setExpectedException(\SS6\ShopBundle\Model\AdvancedSearch\Exception\AdvancedSearchTranslationNotFoundException::class);
		$advancedSearchTranslator->translateFilterName('nonexistingFilterName');
	}

}
