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
