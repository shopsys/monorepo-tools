<?php

namespace Tests\FrameworkBundle\Unit\Model\AdvancedSearch;

use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Model\AdvancedSearch\AdvancedSearchFilterInterface;
use Shopsys\FrameworkBundle\Model\AdvancedSearch\AdvancedSearchRuleData;
use Shopsys\FrameworkBundle\Model\AdvancedSearch\AdvancedSearchService;
use Shopsys\FrameworkBundle\Model\AdvancedSearch\ProductAdvancedSearchConfig;

class AdvancedSearchServiceTest extends TestCase
{
    public function testCreateDefaultRuleFormData()
    {
        $advancedSearchConfigMock = $this->getMockBuilder(ProductAdvancedSearchConfig::class)
            ->setMethods(null)
            ->disableOriginalConstructor()
            ->getMock();
        $filterName = 'filterName';

        $advancedSearchService = new AdvancedSearchService($advancedSearchConfigMock);
        $defaultRuleFormData = $advancedSearchService->createDefaultRuleFormViewData($filterName);

        $this->assertArrayHasKey('subject', $defaultRuleFormData);
        $this->assertArrayHasKey('operator', $defaultRuleFormData);
        $this->assertArrayHasKey('value', $defaultRuleFormData);
        $this->assertSame($filterName, $defaultRuleFormData['subject']);
    }

    public function testGetRulesFormDataByRequestDataDefault()
    {
        $advancedSearchConfigMock = $this->getMockBuilder(ProductAdvancedSearchConfig::class)
            ->setMethods(null)
            ->disableOriginalConstructor()
            ->getMock();

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

    public function testGetRulesFormDataByRequestData()
    {
        $advancedSearchConfigMock = $this->getMockBuilder(ProductAdvancedSearchConfig::class)
            ->setMethods(null)
            ->disableOriginalConstructor()
            ->getMock();

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

    public function testExtendQueryBuilderByAdvancedSearchData()
    {
        $ruleData = new AdvancedSearchRuleData();
        $ruleData->subject = 'testSubject';
        $ruleData->operator = 'testOperator';
        $ruleData->value = 'testValue';

        $advancedSearchData = [
            AdvancedSearchService::TEMPLATE_RULE_FORM_KEY => null,
            0 => $ruleData,
        ];

        $advancedSearchFilterMock = $this->getMockBuilder(AdvancedSearchFilterInterface::class)
            ->setMethods(['extendQueryBuilder'])
            ->getMockForAbstractClass();

        $advancedSearchConfigMock = $this->getMockBuilder(ProductAdvancedSearchConfig::class)
            ->setMethods(['getFilter'])
            ->disableOriginalConstructor()
            ->getMock();
        $advancedSearchConfigMock
            ->expects($this->once())
            ->method('getFilter')
            ->with($this->equalTo($ruleData->subject))
            ->willReturn($advancedSearchFilterMock);

        $queryBuilderMock = $this->getMockBuilder(QueryBuilder::class)
            ->setMethods(null)
            ->disableOriginalConstructor()
            ->getMock();

        $advancedSearchService = new AdvancedSearchService($advancedSearchConfigMock);

        $advancedSearchService->extendQueryBuilderByAdvancedSearchData($queryBuilderMock, $advancedSearchData);
    }
}
