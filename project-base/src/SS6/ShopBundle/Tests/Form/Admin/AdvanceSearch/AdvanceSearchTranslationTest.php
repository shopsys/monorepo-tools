<?php

namespace SS6\ShopBundle\Tests\Form\Admin\AdvanceSearch;

use SS6\ShopBundle\Component\Test\FunctionalTestCase;
use SS6\ShopBundle\Form\Admin\AdvanceSearch\AdvanceSearchTranslation;
use Symfony\Component\Translation\TranslatorInterface;

class AdvanceSearchTranslationTest extends FunctionalTestCase {

	public function testTranslateFilterName() {
		$advanceSearchConfig = $this->getContainer()->get('ss6.shop.advance_search.advance_search_config');
		/* @var $advanceSearchConfig \SS6\ShopBundle\Model\AdvanceSearch\AdvanceSearchConfig */
		$advanceSearchTranslation = $this->getContainer()->get('ss6.shop.form.admin.advance_search.advance_search_translation');
		/* @var $advanceSearchTranslation \SS6\ShopBundle\Form\Admin\AdvanceSearch\AdvanceSearchTranslation */

		foreach ($advanceSearchConfig->getAllFilters() as $filter) {
			$this->assertNotEmpty($advanceSearchTranslation->translateFilterName($filter->getName()));
		}
	}

	public function testTranslateOperator() {
		$advanceSearchConfig = $this->getContainer()->get('ss6.shop.advance_search.advance_search_config');
		/* @var $advanceSearchConfig \SS6\ShopBundle\Model\AdvanceSearch\AdvanceSearchConfig */
		$advanceSearchTranslation = $this->getContainer()->get('ss6.shop.form.admin.advance_search.advance_search_translation');
		/* @var $advanceSearchTranslation \SS6\ShopBundle\Form\Admin\AdvanceSearch\AdvanceSearchTranslation */

		$operators = [];
		foreach ($advanceSearchConfig->getAllFilters() as $filter) {
			$operators = array_merge($operators, $filter->getAllowedOperators());
		}

		foreach ($operators as $operator) {
			$this->assertNotEmpty($advanceSearchTranslation->translateOperator($operator));
		}
	}

	public function testTranslateFilterNameNotFoundException() {
		$translatorMock = $this->getMockBuilder(TranslatorInterface::class)->getMockForAbstractClass();
		$advanceSearchTranslator = new AdvanceSearchTranslation($translatorMock);

		$this->setExpectedException(\SS6\ShopBundle\Model\AdvanceSearch\Exception\AdvanceSearchTranslationNotFoundException::class);
		$advanceSearchTranslator->translateFilterName('nonexistingFilterName');
	}

	public function testTranslateOperatorNotFoundException() {
		$translatorMock = $this->getMockBuilder(TranslatorInterface::class)->getMockForAbstractClass();
		$advanceSearchTranslator = new AdvanceSearchTranslation($translatorMock);

		$this->setExpectedException(\SS6\ShopBundle\Model\AdvanceSearch\Exception\AdvanceSearchTranslationNotFoundException::class);
		$advanceSearchTranslator->translateOperator('nonexistingOperator');
	}
}
