<?php

namespace SS6\ShopBundle\Tests\Unit\Form\Admin\AdvancedSearch;

use SS6\ShopBundle\Component\Translation\Translator;
use SS6\ShopBundle\Form\Admin\AdvancedSearch\AdvancedSearchOrderFilterTranslation;
use SS6\ShopBundle\Model\AdvancedSearch\OrderAdvancedSearchConfig;
use SS6\ShopBundle\Tests\Test\FunctionalTestCase;

class AdvancedSearchOrderFilterTranslationTest extends FunctionalTestCase {

	public function testTranslateFilterName() {
		$advancedSearchConfig = $this->getContainer()->get(OrderAdvancedSearchConfig::class);
		/* @var $advancedSearchConfig \SS6\ShopBundle\Model\AdvancedSearch\OrderAdvancedSearchConfig */
		$advancedSearchOrderFilterTranslation = $this->getContainer()->get(AdvancedSearchOrderFilterTranslation::class);
		// @codingStandardsIgnoreStart
		/* @var $advancedSearchOrderFilterTranslation \SS6\ShopBundle\Form\Admin\AdvancedSearch\AdvancedSearchOrderFilterTranslation */
		// @codingStandardsIgnoreEnd

		foreach ($advancedSearchConfig->getAllFilters() as $filter) {
			$this->assertNotEmpty($advancedSearchOrderFilterTranslation->translateFilterName($filter->getName()));
		}
	}

	public function testTranslateFilterNameNotFoundException() {
		$translatorMock = $this->getMockBuilder(Translator::class)
			->disableOriginalConstructor()
			->getMock();
		$advancedSearchTranslator = new AdvancedSearchOrderFilterTranslation($translatorMock);

		$this->setExpectedException(\SS6\ShopBundle\Model\AdvancedSearch\Exception\AdvancedSearchTranslationNotFoundException::class);
		$advancedSearchTranslator->translateFilterName('nonexistingFilterName');
	}

}
