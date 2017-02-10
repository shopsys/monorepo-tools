<?php

namespace Shopsys\ShopBundle\Tests\Unit\Component\Form;

use Shopsys\ShopBundle\Component\Domain\Config\DomainConfig;
use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Component\Router\DomainRouterFactory;
use Shopsys\ShopBundle\Component\Setting\Setting;
use Shopsys\ShopBundle\Model\Mail\MailTemplate;
use Shopsys\ShopBundle\Model\Mail\MailTemplateData;
use Shopsys\ShopBundle\Model\Mail\MessageData;
use Shopsys\ShopBundle\Model\Order\Item\OrderItemPriceCalculation;
use Shopsys\ShopBundle\Model\Order\Mail\OrderMailService;
use Shopsys\ShopBundle\Model\Order\OrderService;
use Shopsys\ShopBundle\Model\Order\Status\OrderStatus;
use Shopsys\ShopBundle\Tests\Test\FunctionalTestCase;
use Shopsys\ShopBundle\Twig\DateTimeFormatterExtension;
use Shopsys\ShopBundle\Twig\PriceExtension;
use Symfony\Component\Routing\RouterInterface;
use Twig_Environment;

class OrderMailServiceTest extends FunctionalTestCase
{

    public function testGetMailTemplateNameByStatus() {
        $routerMock = $this->getMockBuilder(RouterInterface::class)->setMethods(['generate'])->getMockForAbstractClass();
        $routerMock->expects($this->any())->method('generate')->willReturn('generatedUrl');

        $domainRouterFactoryMock = $this->getMock(DomainRouterFactory::class, ['getRouter'], [], '', false);
        $domainRouterFactoryMock->expects($this->any())->method('getRouter')->willReturn($routerMock);

        $twigMock = $this->getMockBuilder(Twig_Environment::class)
            ->disableOriginalConstructor()
            ->getMock();
        $orderItemPriceCalculationMock = $this->getMockBuilder(OrderItemPriceCalculation::class)
            ->disableOriginalConstructor()
            ->getMock();
        $settingMock = $this->getMockBuilder(Setting::class)
            ->disableOriginalConstructor()
            ->getMock();

        $domainMock = $this->getMockBuilder(Domain::class)
            ->disableOriginalConstructor()
            ->getMock();
        $priceExtensionMock = $this->getMockBuilder(PriceExtension::class)
            ->disableOriginalConstructor()
            ->getMock();
        $dateTimeFormatterExtensionMock = $this->getMockBuilder(DateTimeFormatterExtension::class)
            ->disableOriginalConstructor()
            ->getMock();
        $orderServiceMock = $this->getMockBuilder(OrderService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $orderMailService = new OrderMailService(
            $settingMock,
            $domainRouterFactoryMock,
            $twigMock,
            $orderItemPriceCalculationMock,
            $domainMock,
            $priceExtensionMock,
            $dateTimeFormatterExtensionMock,
            $orderServiceMock
        );

        $orderStatus1 = $this->getMock(OrderStatus::class, ['getId'], [], '', false);
        $orderStatus1->expects($this->atLeastOnce())->method('getId')->willReturn(1);

        $orderStatus2 = $this->getMock(OrderStatus::class, ['getId'], [], '', false);
        $orderStatus2->expects($this->atLeastOnce())->method('getId')->willReturn(2);

        $mailTempleteName1 = $orderMailService->getMailTemplateNameByStatus($orderStatus1);
        $mailTempleteName2 = $orderMailService->getMailTemplateNameByStatus($orderStatus2);

        $this->assertNotEmpty($mailTempleteName1);
        $this->assertInternalType('string', $mailTempleteName1);

        $this->assertNotEmpty($mailTempleteName2);
        $this->assertInternalType('string', $mailTempleteName2);

        $this->assertNotSame($mailTempleteName1, $mailTempleteName2);
    }

    public function testGetMessageByOrder() {
        $routerMock = $this->getMockBuilder(RouterInterface::class)->setMethods(['generate'])->getMockForAbstractClass();
        $routerMock->expects($this->any())->method('generate')->willReturn('generatedUrl');

        $domainRouterFactoryMock = $this->getMock(DomainRouterFactory::class, ['getRouter'], [], '', false);
        $domainRouterFactoryMock->expects($this->any())->method('getRouter')->willReturn($routerMock);

        $twigMock = $this->getMockBuilder(Twig_Environment::class)
            ->disableOriginalConstructor()
            ->getMock();
        $orderItemPriceCalculationMock = $this->getMockBuilder(OrderItemPriceCalculation::class)
            ->disableOriginalConstructor()
            ->getMock();
        $settingMock = $this->getMockBuilder(Setting::class)
            ->disableOriginalConstructor()
            ->getMock();
        $priceExtensionMock = $this->getMockBuilder(PriceExtension::class)
            ->disableOriginalConstructor()
            ->getMock();

        $dateTimeFormatterExtensionMock = $this->getMockBuilder(DateTimeFormatterExtension::class)
            ->disableOriginalConstructor()
            ->getMock();

        $orderServiceMock = $this->getMockBuilder(OrderService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $domainConfig = new DomainConfig(1, 'http://example.com:8080', 'example', 'cs');
        $domain = new Domain([$domainConfig], $settingMock);

        $orderMailService = new OrderMailService(
            $settingMock,
            $domainRouterFactoryMock,
            $twigMock,
            $orderItemPriceCalculationMock,
            $domain,
            $priceExtensionMock,
            $dateTimeFormatterExtensionMock,
            $orderServiceMock
        );

        $order = $this->getReference('order_1');

        $mailTemplateData = new MailTemplateData();
        $mailTemplateData->subject = 'subject';
        $mailTemplateData->body = 'body';
        $mailTemplate = new MailTemplate('templateName', 1, $mailTemplateData);

        $messageData = $orderMailService->getMessageDataByOrder($order, $mailTemplate);

        $this->assertInstanceOf(MessageData::class, $messageData);
        $this->assertSame($mailTemplate->getSubject(), $messageData->subject);
        $this->assertSame($mailTemplate->getBody(), $messageData->body);
    }
}
