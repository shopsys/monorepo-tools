<?php

namespace Shopsys\ShopBundle\Tests\Unit\Component\Cron;

use Shopsys\ShopBundle\Component\Constraints\UniqueSlugsOnDomains;
use Shopsys\ShopBundle\Component\Constraints\UniqueSlugsOnDomainsValidator;
use Shopsys\ShopBundle\Component\Domain\Config\DomainConfig;
use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Component\Router\DomainRouterFactory;
use Shopsys\ShopBundle\Component\Setting\Setting;
use Shopsys\ShopBundle\Form\FriendlyUrlType;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Validator\Tests\Constraints\AbstractConstraintValidatorTest;

class UniqueSlugsOnDomainsValidatorTest extends AbstractConstraintValidatorTest
{
    /**
     * @inheritdoc
     */
    protected function createValidator() {
        $domainConfigs = [
            new DomainConfig(1, 'http://example.cz', 'name1', 'cs'),
            new DomainConfig(2, 'http://example.com', 'name2', 'en'),
        ];
        $settingMock = $this->getMock(Setting::class, [], [], '', false);
        $domain = new Domain($domainConfigs, $settingMock);

        $routerMock = $this->getMockBuilder(RouterInterface::class)
            ->setMethods(['match'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $routerMock->method('match')->willReturnCallback(function ($path) {
            if ($path !== '/existing-url/') {
                throw new \Symfony\Component\Routing\Exception\ResourceNotFoundException();
            }
        });

        $domainRouterFactoryMock = $this->getMock(DomainRouterFactory::class, ['getRouter'], [], '', false);
        $domainRouterFactoryMock->method('getRouter')->willReturn($routerMock);

        return new UniqueSlugsOnDomainsValidator($domain, $domainRouterFactoryMock);
    }

    public function testValidateSameSlugsOnDifferentDomains() {
        $values = [
            [
                FriendlyUrlType::FIELD_DOMAIN => 1,
                FriendlyUrlType::FIELD_SLUG => 'new-url/',
            ],
            [
                FriendlyUrlType::FIELD_DOMAIN => 2,
                FriendlyUrlType::FIELD_SLUG => 'new-url/',
            ],
        ];
        $constraint = new UniqueSlugsOnDomains();

        $this->validator->validate($values, $constraint);
        $this->assertNoViolation();
    }

    public function testValidateDuplicateSlugsOnSameDomain() {
        $values = [
            [
                FriendlyUrlType::FIELD_DOMAIN => 1,
                FriendlyUrlType::FIELD_SLUG => 'new-url/',
            ],
            [
                FriendlyUrlType::FIELD_DOMAIN => 1,
                FriendlyUrlType::FIELD_SLUG => 'new-url/',
            ],
        ];
        $constraint = new UniqueSlugsOnDomains();
        $constraint->messageDuplicate = 'myMessage';

        $this->validator->validate($values, $constraint);

        $this->buildViolation('myMessage')
            ->setParameter('{{ url }}', 'http://example.cz/new-url/')
            ->assertRaised();
    }

    public function testValidateExistingSlug() {
        $values = [
            [
                FriendlyUrlType::FIELD_DOMAIN => 1,
                FriendlyUrlType::FIELD_SLUG => 'existing-url/',
            ],
        ];
        $constraint = new UniqueSlugsOnDomains();
        $constraint->message = 'myMessage';

        $this->validator->validate($values, $constraint);

        $this->buildViolation('myMessage')
            ->setParameter('{{ url }}', 'http://example.cz/existing-url/')
            ->assertRaised();
    }
}
