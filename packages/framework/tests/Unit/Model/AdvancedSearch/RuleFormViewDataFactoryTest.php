<?php

namespace Tests\FrameworkBundle\Unit\Model\AdvancedSearch;

use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Model\AdvancedSearch\RuleFormViewDataFactory;

class RuleFormViewDataFactoryTest extends TestCase
{
    public function testCreateDefault()
    {
        $filterName = 'filterName';

        $ruleFormViewDataFactory = new RuleFormViewDataFactory();
        $defaultRuleFormData = $ruleFormViewDataFactory->createDefault($filterName);

        $this->assertArrayHasKey('subject', $defaultRuleFormData);
        $this->assertArrayHasKey('operator', $defaultRuleFormData);
        $this->assertArrayHasKey('value', $defaultRuleFormData);
        $this->assertSame($filterName, $defaultRuleFormData['subject']);
    }

    public function testCreateFromRequestDataDefault()
    {
        $ruleFormViewDataFactory = new RuleFormViewDataFactory();
        $rulesFormViewData = $ruleFormViewDataFactory->createFromRequestData('productName');

        $this->assertArrayHasKey(RuleFormViewDataFactory::TEMPLATE_RULE_FORM_KEY, $rulesFormViewData);
        $this->assertCount(2, $rulesFormViewData);
        foreach ($rulesFormViewData as $ruleFormData) {
            $this->assertArrayHasKey('subject', $ruleFormData);
            $this->assertArrayHasKey('operator', $ruleFormData);
            $this->assertArrayHasKey('value', $ruleFormData);
        }
    }

    public function testCreateFromRequestData()
    {
        $requestData = [
            [
                'subject' => 'testSubject',
                'operator' => 'testOperator',
                'value' => 'testValue',
            ],
        ];

        $ruleFormViewDataFactory = new RuleFormViewDataFactory();
        $rulesFormViewData = $ruleFormViewDataFactory->createFromRequestData('productName', $requestData);

        $this->assertArrayHasKey(RuleFormViewDataFactory::TEMPLATE_RULE_FORM_KEY, $rulesFormViewData);
        $this->assertCount(2, $rulesFormViewData);
        foreach ($rulesFormViewData as $key => $ruleFormData) {
            if ($key !== RuleFormViewDataFactory::TEMPLATE_RULE_FORM_KEY) {
                $this->assertSame($requestData[0], $ruleFormData);
            }
        }
    }
}
