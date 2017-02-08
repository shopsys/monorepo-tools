<?php

namespace SS6\ShopBundle\Tests\Unit\Form\Admin\AdvancedSearch;

use SS6\ShopBundle\Form\Admin\AdvancedSearch\AdvancedSearchOperatorTranslation;
use SS6\ShopBundle\Model\AdvancedSearch\ProductAdvancedSearchConfig;
use SS6\ShopBundle\Tests\Test\FunctionalTestCase;

class AdvancedSearchOperatorTranslationTest extends FunctionalTestCase {

	public function testTranslateOperator() {
		$productAdvancedSearchConfig = $this->getContainer()->get(ProductAdvancedSearchConfig::class);
		/* @var $productAdvancedSearchConfig \SS6\ShopBundle\Model\AdvancedSearch\ProductAdvancedSearchConfig */
		$orderAdvancedSearchConfig = $this->getContainer()->get(ProductAdvancedSearchConfig::class);
		/* @var $orderAdvancedSearchConfig \SS6\ShopBundle\Model\AdvancedSearch\OrderAdvancedSearchConfig */

		$advancedSearchOperatorTranslation = $this->getContainer()->get(AdvancedSearchOperatorTranslation::class);
		/* @var $advancedSearchOperatorTranslation \SS6\ShopBundle\Form\Admin\AdvancedSearch\AdvancedSearchOperatorTranslation */

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

	public function testTranslateOperatorNotFoundException() {
		$advancedSearchTranslator = new AdvancedSearchOperatorTranslation();

		$this->setExpectedException(\SS6\ShopBundle\Model\AdvancedSearch\Exception\AdvancedSearchTranslationNotFoundException::class);
		$advancedSearchTranslator->translateOperator('nonexistingOperator');
	}

}
