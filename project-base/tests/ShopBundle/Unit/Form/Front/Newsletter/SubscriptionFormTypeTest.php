<?php

namespace Tests\ShopBundle\Unit\Form\Front\Newsletter;

use PHPUnit\Framework\Assert;
use Shopsys\ShopBundle\Form\Front\Newsletter\SubscriptionFormType;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Validator\Validation;

class SubscriptionFormTypeTest extends TypeTestCase
{
    public function testValid(): void
    {
        $data = [
            'email' => 'no-reply@shopsys.com',
            'email2' => null,
            'privacyPolicyAgreement' => true,
        ];

        $form = $this->factory->create(SubscriptionFormType::class);
        $form->submit($data);
        Assert::assertTrue($form->isValid());

        $expectedData = [
            'email' => 'no-reply@shopsys.com',
        ];
        Assert::assertSame($expectedData, $form->getData());
    }

    /**
     * @param array $data
     * @dataProvider getInvalidTestCases
     */
    public function testInvalid(array $data): void
    {
        $form = $this->factory->create(SubscriptionFormType::class);
        $form->submit($data);
        Assert::assertFalse($form->isValid());
    }

    /**
     * @return array
     */
    public function getInvalidTestCases(): array
    {
        return [
            [
                'data' => [
                    'email' => null,
                ],
            ],
            [
                'data' => [
                    'email' => '',
                ],
            ],
            [
                'data' => [
                    'email' => 'invalid-email',
                ],
            ],
            [
                'data' => [
                    'email' => 'no-reply@shopsys.com',
                    'email2' => 'honeypot-filled',
                ],
            ],
            [
                'data' => [
                    'email' => 'no-reply@shopsys.com',
                    'privacyPolicyAgreement' => false,
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    protected function getExtensions(): array
    {
        return [
            new ValidatorExtension(Validation::createValidator()),
        ];
    }
}
