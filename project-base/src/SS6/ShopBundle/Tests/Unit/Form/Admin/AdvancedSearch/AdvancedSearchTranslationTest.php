<?php

namespace SS6\ShopBundle\Tests\Unit\Form\Admin\AdvancedSearch;

use SS6\ShopBundle\Component\Translation\Translator;
use SS6\ShopBundle\Form\Admin\AdvancedSearch\AdvancedSearchTranslation;
use SS6\ShopBundle\Model\AdvancedSearch\ProductAdvancedSearchConfig;
use SS6\ShopBundle\Tests\Test\FunctionalTestCase;

class AdvancedSearchTranslationTest extends FunctionalTestCase {

	public function testTranslateFilterName() {
		$advancedSearchConfig = $this->getContainer()->get(ProductAdvancedSearchConfig::class);
		/* @var $advancedSearchConfig \SS6\ShopBundle\Model\AdvancedSearch\ProductAdvancedSearchConfig */
		$advancedSearchTranslation = $this->getContainer()->get('ss6.shop.form.admin.advanced_search.advanced_search_translation');
		/* @var $advancedSearchTranslation \SS6\ShopBundle\Form\Admin\AdvancedSearch\AdvancedSearchTranslation */

		foreach ($advancedSearchConfig->getAllFilters() as $filter) {
			$this->assertNotEmpty($advancedSearchTranslation->translateFilterName($filter->getName()));
		}
	}

	public function testTranslateOperator() {
		$advancedSearchConfig = $this->getContainer()->get(ProductAdvancedSearchConfig::class);
		/* @var $advancedSearchConfig \SS6\ShopBundle\Model\AdvancedSearch\ProductAdvancedSearchConfig */
		$advancedSearchTranslation = $this->getContainer()->get('ss6.shop.form.admin.advanced_search.advanced_search_translation');
		/* @var $advancedSearchTranslation \SS6\ShopBundle\Form\Admin\AdvancedSearch\AdvancedSearchTranslation */

		$operators = [];
		foreach ($advancedSearchConfig->getAllFilters() as $filter) {
			$operators = array_merge($operators, $filter->getAllowedOperators());
		}

		foreach ($operators as $operator) {
			$this->assertNotEmpty($advancedSearchTranslation->translateOperator($operator));
		}
	}

	public function testTranslateFilterNameNotFoundException() {
		$translatorMock = $this->getMockBuilder(Translator::class)
			->disableOriginalConstructor()
			->getMock();
		$advancedSearchTranslator = new AdvancedSearchTranslation($translatorMock);

		$this->setExpectedException(\SS6\ShopBundle\Model\AdvancedSearch\Exception\AdvancedSearchTranslationNotFoundException::class);
		$advancedSearchTranslator->translateFilterName('nonexistingFilterName');
	}

	public function testTranslateOperatorNotFoundException() {
		$translatorMock = $this->getMockBuilder(Translator::class)
			->disableOriginalConstructor()
			->getMock();
		$advancedSearchTranslator = new AdvancedSearchTranslation($translatorMock);

		$this->setExpectedException(\SS6\ShopBundle\Model\AdvancedSearch\Exception\AdvancedSearchTranslationNotFoundException::class);
		$advancedSearchTranslator->translateOperator('nonexistingOperator');
	}
}
