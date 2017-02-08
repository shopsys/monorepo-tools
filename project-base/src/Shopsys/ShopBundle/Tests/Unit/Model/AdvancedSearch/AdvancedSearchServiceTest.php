<?php

namespace Shopsys\ShopBundle\Tests\Unit\Model\Order;

use Doctrine\ORM\QueryBuilder;
use PHPUnit_Framework_TestCase;
use Shopsys\ShopBundle\Model\AdvancedSearch\AdvancedSearchFilterInterface;
use Shopsys\ShopBundle\Model\AdvancedSearch\AdvancedSearchRuleData;
use Shopsys\ShopBundle\Model\AdvancedSearch\AdvancedSearchService;
use Shopsys\ShopBundle\Model\AdvancedSearch\ProductAdvancedSearchConfig;

class AdvancedSearchServiceTest extends PHPUnit_Framework_TestCase {

	public function testCreateDefaultRuleFormData() {
		$advancedSearchConfigMock = $this->getMock(ProductAdvancedSearchConfig::class, null, [], '', false);
		$filterName = 'filterName';

		$advancedSearchService = new AdvancedSearchService($advancedSearchConfigMock);
		$defaultRuleFormData = $advancedSearchService->createDefaultRuleFormViewData($filterName);

		$this->assertArrayHasKey('subject', $defaultRuleFormData);
		$this->assertArrayHasKey('operator', $defaultRuleFormData);
		$this->assertArrayHasKey('value', $defaultRuleFormData);
		$this->assertSame($filterName, $defaultRuleFormData['subject']);
	}

	public function testGetRulesFormDataByRequestDataDefault() {
		$advancedSearchConfigMock = $this->getMock(ProductAdvancedSearchConfig::class, null, [], '', false);

		$advancedSearchService = new AdvancedSearchService($advancedSearchConfigMock);
		$rulesFormViewData = $advancedSearchService->getRulesFormViewDataByRequestData(null);

		$this->assertArrayHasKey(AdvancedSearchService::TEMPLATE_RULE_FORM_KEY, $rulesFormViewData);
		$this->assertCount(2, $rulesFormViewData);
		foreach ($rulesFormViewData as $ruleFormData) {
			$this->assertArrayHasKey('subject', $ruleFormData);
			$this->assertArrayHasKey('operator', $ruleFormData);
			$this->assertArrayHasKey('value', $ruleFormData);
		}
	}

	public function testGetRulesFormDataByRequestData() {
		$advancedSearchConfigMock = $this->getMock(ProductAdvancedSearchConfig::class, null, [], '', false);

		$requestData = [
			[
				'subject' => 'testSubject',
				'operator' => 'testOperator',
				'value' => 'testValue',
			],
		];

		$advancedSearchService = new AdvancedSearchService($advancedSearchConfigMock);
		$rulesFormViewData = $advancedSearchService->getRulesFormViewDataByRequestData($requestData);

		$this->assertArrayHasKey(AdvancedSearchService::TEMPLATE_RULE_FORM_KEY, $rulesFormViewData);
		$this->assertCount(2, $rulesFormViewData);
		foreach ($rulesFormViewData as $key => $ruleFormData) {
			if ($key !== AdvancedSearchService::TEMPLATE_RULE_FORM_KEY) {
				$this->assertSame($requestData[0], $ruleFormData);
			}
		}
	}

	public function testExtendQueryBuilderByAdvancedSearchData() {
		$ruleData = new AdvancedSearchRuleData('testSubject', 'testOperator', 'testValue');

		$advancedSearchData = [
			AdvancedSearchService::TEMPLATE_RULE_FORM_KEY => null,
			0 => $ruleData,
		];

		$advancedSearchFilterMock = $this->getMockBuilder(AdvancedSearchFilterInterface::class)
			->setMethods(['extendQueryBuilder'])
			->getMockForAbstractClass();

		$advancedSearchConfigMock = $this->getMock(ProductAdvancedSearchConfig::class, ['getFilter'], [], '', false);
		$advancedSearchConfigMock
			->expects($this->once())
			->method('getFilter')
			->with($this->equalTo($ruleData->subject))
			->willReturn($advancedSearchFilterMock);

		$queryBuilderMock = $this->getMock(QueryBuilder::class, null, [], '', false);

		$advancedSearchService = new AdvancedSearchService($advancedSearchConfigMock);

		$advancedSearchService->extendQueryBuilderByAdvancedSearchData($queryBuilderMock, $advancedSearchData);
	}
}
