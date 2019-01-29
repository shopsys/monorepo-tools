<?php

namespace Tests\ShopBundle\Functional\Model\Order;

use Shopsys\FrameworkBundle\Component\DataFixture\PersistentReferenceFacade;
use Shopsys\FrameworkBundle\DataFixtures\Demo\CountryDataFixture;
use Shopsys\FrameworkBundle\DataFixtures\Demo\CurrencyDataFixture;
use Shopsys\FrameworkBundle\DataFixtures\Demo\OrderStatusDataFixture;
use Shopsys\FrameworkBundle\Model\Cart\CartFacade;
use Shopsys\FrameworkBundle\Model\Cart\Item\CartItemFactory;
use Shopsys\FrameworkBundle\Model\Order\Item\OrderItemData;
use Shopsys\FrameworkBundle\Model\Order\OrderDataFactoryInterface;
use Shopsys\FrameworkBundle\Model\Order\OrderFacade;
use Shopsys\FrameworkBundle\Model\Order\OrderRepository;
use Shopsys\FrameworkBundle\Model\Order\Preview\OrderPreviewFactory;
use Shopsys\FrameworkBundle\Model\Payment\PaymentRepository;
use Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceCalculationForUser;
use Shopsys\FrameworkBundle\Model\Product\ProductRepository;
use Shopsys\FrameworkBundle\Model\Transport\TransportRepository;
use Shopsys\ShopBundle\Model\Order\OrderData;
use Tests\ShopBundle\Test\TransactionFunctionalTestCase;

class OrderFacadeTest extends TransactionFunctionalTestCase
{
    public function testCreate()
    {
        /** @var \Shopsys\FrameworkBundle\Model\Cart\CartFacade $cartFacade */
        $cartFacade = $this->getContainer()->get(CartFacade::class);
        /** @var \Shopsys\FrameworkBundle\Model\Order\OrderFacade $orderFacade */
        $orderFacade = $this->getContainer()->get(OrderFacade::class);
        /** @var \Shopsys\FrameworkBundle\Model\Order\Preview\OrderPreviewFactory $orderPreviewFactory */
        $orderPreviewFactory = $this->getContainer()->get(OrderPreviewFactory::class);
        /** @var \Shopsys\FrameworkBundle\Model\Order\OrderRepository $orderRepository */
        $orderRepository = $this->getContainer()->get(OrderRepository::class);
        /** @var \Shopsys\FrameworkBundle\Model\Product\ProductRepository $productRepository */
        $productRepository = $this->getContainer()->get(ProductRepository::class);
        /** @var \Shopsys\FrameworkBundle\Model\Transport\TransportRepository $transportRepository */
        $transportRepository = $this->getContainer()->get(TransportRepository::class);
        /** @var \Shopsys\FrameworkBundle\Model\Payment\PaymentRepository $paymentRepository */
        $paymentRepository = $this->getContainer()->get(PaymentRepository::class);
        /** @var \Shopsys\FrameworkBundle\Component\DataFixture\PersistentReferenceFacade $persistentReferenceFacade */
        $persistentReferenceFacade = $this->getContainer()->get(PersistentReferenceFacade::class);
        /** @var \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceCalculationForUser $productPriceCalculation */
        $productPriceCalculation = $this->getContainer()->get(ProductPriceCalculationForUser::class);
        /** @var \Shopsys\FrameworkBundle\Model\Cart\Item\CartItemFactory $cartItemFactory */
        $cartItemFactory = $this->getContainer()->get(CartItemFactory::class);

        $cart = $cartFacade->getCartOfCurrentCustomerCreateIfNotExists();
        $product = $productRepository->getById(1);

        $cart->addProduct($product, 1, $productPriceCalculation, $cartItemFactory);

        $transport = $transportRepository->getById(1);
        $payment = $paymentRepository->getById(1);

        $orderData = new OrderData();
        $orderData->transport = $transport;
        $orderData->payment = $payment;
        $orderData->status = $persistentReferenceFacade->getReference(OrderStatusDataFixture::ORDER_STATUS_NEW);
        $orderData->firstName = 'firstName';
        $orderData->lastName = 'lastName';
        $orderData->email = 'email';
        $orderData->telephone = 'telephone';
        $orderData->companyName = 'companyName';
        $orderData->companyNumber = 'companyNumber';
        $orderData->companyTaxNumber = 'companyTaxNumber';
        $orderData->street = 'street';
        $orderData->city = 'city';
        $orderData->postcode = 'postcode';
        $orderData->country = $persistentReferenceFacade->getReference(CountryDataFixture::COUNTRY_CZECH_REPUBLIC_1);
        $orderData->deliveryAddressSameAsBillingAddress = false;
        $orderData->deliveryFirstName = 'deliveryFirstName';
        $orderData->deliveryLastName = 'deliveryLastName';
        $orderData->deliveryCompanyName = 'deliveryCompanyName';
        $orderData->deliveryTelephone = 'deliveryTelephone';
        $orderData->deliveryStreet = 'deliveryStreet';
        $orderData->deliveryCity = 'deliveryCity';
        $orderData->deliveryPostcode = 'deliveryPostcode';
        $orderData->deliveryCountry = $persistentReferenceFacade->getReference(CountryDataFixture::COUNTRY_CZECH_REPUBLIC_1);
        $orderData->note = 'note';
        $orderData->domainId = 1;
        $orderData->currency = $this->getReference(CurrencyDataFixture::CURRENCY_CZK);

        $orderPreview = $orderPreviewFactory->createForCurrentUser($transport, $payment);
        $order = $orderFacade->createOrder($orderData, $orderPreview, null);

        $orderFromDb = $orderRepository->getById($order->getId());

        $this->assertSame($orderData->transport->getId(), $orderFromDb->getTransport()->getId());
        $this->assertSame($orderData->payment->getId(), $orderFromDb->getPayment()->getId());
        $this->assertSame($orderData->firstName, $orderFromDb->getFirstName());
        $this->assertSame($orderData->lastName, $orderFromDb->getLastName());
        $this->assertSame($orderData->email, $orderFromDb->getEmail());
        $this->assertSame($orderData->telephone, $orderFromDb->getTelephone());
        $this->assertSame($orderData->companyName, $orderFromDb->getCompanyName());
        $this->assertSame($orderData->companyNumber, $orderFromDb->getCompanyNumber());
        $this->assertSame($orderData->companyTaxNumber, $orderFromDb->getCompanyTaxNumber());
        $this->assertSame($orderData->street, $orderFromDb->getStreet());
        $this->assertSame($orderData->city, $orderFromDb->getCity());
        $this->assertSame($orderData->postcode, $orderFromDb->getPostcode());
        $this->assertSame($orderData->country, $orderFromDb->getCountry());
        $this->assertSame($orderData->deliveryFirstName, $orderFromDb->getDeliveryFirstName());
        $this->assertSame($orderData->deliveryLastName, $orderFromDb->getDeliveryLastName());
        $this->assertSame($orderData->deliveryCompanyName, $orderFromDb->getDeliveryCompanyName());
        $this->assertSame($orderData->deliveryTelephone, $orderFromDb->getDeliveryTelephone());
        $this->assertSame($orderData->deliveryStreet, $orderFromDb->getDeliveryStreet());
        $this->assertSame($orderData->deliveryCity, $orderFromDb->getDeliveryCity());
        $this->assertSame($orderData->deliveryPostcode, $orderFromDb->getDeliveryPostcode());
        $this->assertSame($orderData->deliveryCountry, $orderFromDb->getDeliveryCountry());
        $this->assertSame($orderData->note, $orderFromDb->getNote());
        $this->assertSame($orderData->domainId, $orderFromDb->getDomainId());

        $this->assertCount(3, $orderFromDb->getItems());
    }

    public function testEdit()
    {
        /** @var \Shopsys\FrameworkBundle\Model\Order\OrderFacade $orderFacade */
        $orderFacade = $this->getContainer()->get(OrderFacade::class);
        /** @var \Shopsys\FrameworkBundle\Model\Order\OrderRepository $orderRepository */
        $orderRepository = $this->getContainer()->get(OrderRepository::class);
        /** @var \Shopsys\ShopBundle\Model\Order\OrderDataFactory $orderDataFactory */
        $orderDataFactory = $this->getContainer()->get(OrderDataFactoryInterface::class);

        /** @var \Shopsys\ShopBundle\Model\Order\Order $order */
        $order = $this->getReference('order_1');

        $this->assertCount(4, $order->getItems());

        $orderData = $orderDataFactory->createFromOrder($order);

        $orderItemsData = $orderData->itemsWithoutTransportAndPayment;
        array_pop($orderItemsData);

        $orderItemData1 = new OrderItemData();
        $orderItemData1->name = 'itemName1';
        $orderItemData1->priceWithoutVat = 100;
        $orderItemData1->priceWithVat = 121;
        $orderItemData1->vatPercent = 21;
        $orderItemData1->quantity = 3;

        $orderItemData2 = new OrderItemData();
        $orderItemData2->name = 'itemName2';
        $orderItemData2->priceWithoutVat = 333;
        $orderItemData2->priceWithVat = 333;
        $orderItemData2->vatPercent = 0;
        $orderItemData2->quantity = 1;

        $orderItemsData[OrderData::NEW_ITEM_PREFIX . '1'] = $orderItemData1;
        $orderItemsData[OrderData::NEW_ITEM_PREFIX . '2'] = $orderItemData2;

        $orderData->itemsWithoutTransportAndPayment = $orderItemsData;
        $orderFacade->edit($order->getId(), $orderData);

        $orderFromDb = $orderRepository->getById($order->getId());

        $this->assertCount(5, $orderFromDb->getItems());
    }
}
