<?php

namespace Tests\FrameworkBundle\Unit\Model\Currency;

use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\Setting\Setting;
use Shopsys\FrameworkBundle\Model\Order\OrderRepository;
use Shopsys\FrameworkBundle\Model\Payment\PaymentPriceFactory;
use Shopsys\FrameworkBundle\Model\Payment\PaymentRepository;
use Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency;
use Shopsys\FrameworkBundle\Model\Pricing\Currency\CurrencyFacade;
use Shopsys\FrameworkBundle\Model\Pricing\Currency\CurrencyRepository;
use Shopsys\FrameworkBundle\Model\Pricing\Currency\CurrencyService;
use Shopsys\FrameworkBundle\Model\Pricing\PricingSetting;
use Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceRecalculationScheduler;
use Shopsys\FrameworkBundle\Model\Transport\TransportPriceFactory;
use Shopsys\FrameworkBundle\Model\Transport\TransportRepository;

class CurrencyFacadeTest extends TestCase
{
    public function testGetDomainConfigsForCurrency()
    {
        $entityManagerMock = $this->createMock(EntityManager::class);
        $currencyRepositoryMock = $this->createMock(CurrencyRepository::class);
        $currencyServiceMock = $this->createMock(CurrencyService::class);
        $orderRepoistoryMock = $this->createMock(OrderRepository::class);
        $productPriceRecalculationShedulerMock = $this->createMock(ProductPriceRecalculationScheduler::class);
        $paymentRepositoryMock = $this->createMock(PaymentRepository::class);
        $transportRepositoryMock = $this->createMock(TransportRepository::class);
        $paymentPriceFactoryMock = $this->createMock(PaymentPriceFactory::class);
        $transportPriceFactoryMock = $this->createMock(TransportPriceFactory::class);

        $testDomainConfigs = [
            1 => new DomainConfig(1, 'http://example.com:8080', 'example', 'cs'),
            2 => new DomainConfig(2, 'http://example.org:8080', 'example.org', 'en'),
            3 => new DomainConfig(3, 'http://example.edu:8080', 'example.edu', 'en'),
        ];
        $settingMock = $this->createMock(Setting::class);

        $pricingSettingMock = $this->getMockBuilder(PricingSetting::class)
            ->setMethods(['getDomainDefaultCurrencyIdByDomainId'])
            ->disableOriginalConstructor()
            ->getMock();
        $pricingSettingMock
            ->expects($this->any())
            ->method('getDomainDefaultCurrencyIdByDomainId')
            ->willReturnMap([
                [1, 1],
                [2, 2],
                [3, 1],
            ]);

        $domain = new Domain($testDomainConfigs, $settingMock);

        $currencyFacade = new CurrencyFacade(
            $entityManagerMock,
            $currencyRepositoryMock,
            $currencyServiceMock,
            $pricingSettingMock,
            $orderRepoistoryMock,
            $domain,
            $productPriceRecalculationShedulerMock,
            $paymentRepositoryMock,
            $transportRepositoryMock,
            $paymentPriceFactoryMock,
            $transportPriceFactoryMock
        );

        $currencyMock = $this->getMockBuilder(Currency::class)
            ->setMethods(['getId'])
            ->disableOriginalConstructor()
            ->getMock();
        $currencyMock->expects($this->any())->method('getId')->willReturn(1);

        $domainConfigs = $currencyFacade->getDomainConfigsByCurrency($currencyMock);

        $this->assertCount(2, $domainConfigs);
        $this->assertContains($testDomainConfigs[1], $domainConfigs);
        $this->assertContains($testDomainConfigs[3], $domainConfigs);
    }
}
