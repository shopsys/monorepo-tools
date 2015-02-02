<?php

namespace SS6\ShopBundle\Tests\Model\Order;

use Doctrine\ORM\QueryBuilder;
use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\AdvanceSearch\AdvanceSearchConfig;
use SS6\ShopBundle\Model\AdvanceSearch\AdvanceSearchFilterInterface;
use SS6\ShopBundle\Model\AdvanceSearch\AdvanceSearchService;

class AdvanceSearchServiceTest extends PHPUnit_Framework_TestCase {

	public function testCreateDefaultRuleFormData() {
		$advanceSearchConfigMock = $this->getMock(AdvanceSearchConfig::class, null, [], '', false);
		$filterName = 'filterName';

		$advanceSearchService = new AdvanceSearchService($advanceSearchConfigMock);
		$defaultRuleFormData = $advanceSearchService->createDefaultRuleFormData($filterName);

		$this->assertArrayHasKey('subject', $defaultRuleFormData);
		$this->assertArrayHasKey('operator', $defaultRuleFormData);
		$this->assertArrayHasKey('value', $defaultRuleFormData);
		$this->assertEquals($filterName, $defaultRuleFormData['subject']);
	}

	public function testGetRulesFormDataByRequestDataDefault() {
		$advanceSearchConfigMock = $this->getMock(AdvanceSearchConfig::class, null, [], '', false);

		$advanceSearchService = new AdvanceSearchService($advanceSearchConfigMock);
		$rulesFormData = $advanceSearchService->getRulesFormDataByRequestData(null);

		$this->assertArrayHasKey(AdvanceSearchService::TEMPLATE_RULE_FORM_KEY, $rulesFormData);
		$this->assertCount(2, $rulesFormData);
		foreach ($rulesFormData as $ruleFormData) {
			$this->assertArrayHasKey('subject', $ruleFormData);
			$this->assertArrayHasKey('operator', $ruleFormData);
			$this->assertArrayHasKey('value', $ruleFormData);
		}
	}

	public function testGetRulesFormDataByRequestData() {
		$advanceSearchConfigMock = $this->getMock(AdvanceSearchConfig::class, null, [], '', false);

		$requestData = [
			[
				'subject' => 'testSubject',
				'operator' => 'testOperator',
				'value' => 'testValue',
			],
		];

		$advanceSearchService = new AdvanceSearchService($advanceSearchConfigMock);
		$rulesFormData = $advanceSearchService->getRulesFormDataByRequestData($requestData);

		$this->assertArrayHasKey(AdvanceSearchService::TEMPLATE_RULE_FORM_KEY, $rulesFormData);
		$this->assertCount(2, $rulesFormData);
		foreach ($rulesFormData as $key => $ruleFormData) {
			if ($key !== AdvanceSearchService::TEMPLATE_RULE_FORM_KEY) {
				$this->assertEquals($requestData[0], $ruleFormData);
			}
		}
	}

	public function testExtendQueryBuilderByAdvanceSearchData() {
		$ruleData = [
			'subject' => 'testSubject',
			'operator' => 'testOperator',
			'value' => 'testValue',
		];

		$advanceSearchData = [
			AdvanceSearchService::TEMPLATE_RULE_FORM_KEY => null,
			0 => $ruleData,
		];

		$advanceSearchFilterMock = $this->getMockBuilder(AdvanceSearchFilterInterface::class)
			->setMethods(['extendQueryBuilder'])
			->getMockForAbstractClass();

		$advanceSearchConfigMock = $this->getMock(AdvanceSearchConfig::class, ['getFilter'], [], '', false);
		$advanceSearchConfigMock
			->expects($this->once())
			->method('getFilter')
			->with($this->equalTo($ruleData['subject']))
			->willReturn($advanceSearchFilterMock);

		$queryBuilderMock = $this->getMock(QueryBuilder::class, null, [], '', false);

		$advanceSearchService = new AdvanceSearchService($advanceSearchConfigMock);

		$advanceSearchService->extendQueryBuilderByAdvanceSearchData($queryBuilderMock, $advanceSearchData);
	}
}
