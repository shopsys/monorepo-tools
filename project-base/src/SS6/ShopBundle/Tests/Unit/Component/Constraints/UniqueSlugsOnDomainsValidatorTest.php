<?php

namespace SS6\ShopBundle\Tests\Unit\Component\Cron;

use SS6\ShopBundle\Component\Constraints\UniqueSlugsOnDomains;
use SS6\ShopBundle\Component\Constraints\UniqueSlugsOnDomainsValidator;
use SS6\ShopBundle\Component\Domain\Config\DomainConfig;
use SS6\ShopBundle\Component\Domain\Domain;
use SS6\ShopBundle\Component\Router\DomainRouterFactory;
use SS6\ShopBundle\Component\Setting\Setting;
use SS6\ShopBundle\Form\FriendlyUrlType;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Validator\Tests\Constraints\AbstractConstraintValidatorTest;

/**
 * @UglyTest
 */
class UniqueSlugsOnDomainsValidatorTest extends AbstractConstraintValidatorTest {

	/**
	 * @inheritdoc
	 */
	protected function createValidator() {
		$domainConfigs = [
			new DomainConfig(1, 'http://example.cz', 'name1', 'cs', 'stylesDirectory'),
			new DomainConfig(2, 'http://example.com', 'name2', 'en', 'stylesDirectory'),
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
