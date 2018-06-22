<?php

namespace Tests\FrameworkBundle\Unit\Component\Constraints;

use Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\Router\DomainRouterFactory;
use Shopsys\FrameworkBundle\Component\Setting\Setting;
use Shopsys\FrameworkBundle\Form\Constraints\UniqueSlugsOnDomains;
use Shopsys\FrameworkBundle\Form\Constraints\UniqueSlugsOnDomainsValidator;
use Shopsys\FrameworkBundle\Form\FriendlyUrlType;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class UniqueSlugsOnDomainsValidatorTest extends ConstraintValidatorTestCase
{
    /**
     * @inheritdoc
     */
    protected function createValidator()
    {
        $domainConfigs = [
            new DomainConfig(1, 'http://example.cz', 'name1', 'cs'),
            new DomainConfig(2, 'http://example.com', 'name2', 'en'),
        ];
        $settingMock = $this->createMock(Setting::class);
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

        $domainRouterFactoryMock = $this->getMockBuilder(DomainRouterFactory::class)
            ->setMethods(['getRouter'])
            ->disableOriginalConstructor()
            ->getMock();
        $domainRouterFactoryMock->method('getRouter')->willReturn($routerMock);

        return new UniqueSlugsOnDomainsValidator($domain, $domainRouterFactoryMock);
    }

    public function testValidateSameSlugsOnDifferentDomains()
    {
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

    public function testValidateDuplicateSlugsOnSameDomain()
    {
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

    public function testValidateExistingSlug()
    {
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
