<?php

namespace Shopsys\ShopBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\DataFixtures\Base\CurrencyDataFixture;
use Shopsys\ShopBundle\DataFixtures\Base\OrderStatusDataFixture;
use Shopsys\ShopBundle\DataFixtures\Demo\CountryDataFixture;
use Shopsys\ShopBundle\DataFixtures\Demo\ProductDataFixture;
use Shopsys\ShopBundle\Model\Customer\User;
use Shopsys\ShopBundle\Model\Customer\UserRepository;
use Shopsys\ShopBundle\Model\Order\Item\QuantifiedProduct;
use Shopsys\ShopBundle\Model\Order\OrderData;
use Shopsys\ShopBundle\Model\Order\OrderFacade;
use Shopsys\ShopBundle\Model\Order\Preview\OrderPreviewFactory;

/**
 * @SuppressWarnings(PHPMD.ExcessiveClassLength)
 */
class OrderDataFixture extends AbstractReferenceFixture implements DependentFixtureInterface {

	const ORDER_PREFIX = 'order_';

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 *
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	public function load(ObjectManager $manager) {
		$userRepository = $this->get(UserRepository::class);
		/* @var $userRepository \Shopsys\ShopBundle\Model\Customer\UserRepository */

		$user = $userRepository->findUserByEmailAndDomain('no-reply@netdevelo.cz', 1);
		$orderData = new OrderData();
		$orderData->transport = $this->getReference(TransportDataFixture::TRANSPORT_PERSONAL);
		$orderData->payment = $this->getReference(PaymentDataFixture::PAYMENT_CASH);
		$orderData->status = $this->getReference(OrderStatusDataFixture::ORDER_STATUS_DONE);
		$orderData->firstName = 'Jiří';
		$orderData->lastName = 'Ševčík';
		$orderData->email = 'no-reply@netdevelo.cz';
		$orderData->telephone = '+420369554147';
		$orderData->street = 'První 1';
		$orderData->city = 'Ostrava';
		$orderData->postcode = '71200';
		$orderData->country = $this->getReference(CountryDataFixture::COUNTRY_CZECH_REPUBLIC_1);
		$orderData->deliveryAddressSameAsBillingAddress = true;
		$orderData->domainId = Domain::FIRST_DOMAIN_ID;
		$orderData->currency = $this->getReference(CurrencyDataFixture::CURRENCY_CZK);
		$this->createOrder(
			$orderData,
			[
				ProductDataFixture::PRODUCT_PREFIX . '9' => 2,
				ProductDataFixture::PRODUCT_PREFIX . '10' => 3,
			],
			$user
		);

		$orderData = new OrderData();
		$orderData->transport = $this->getReference(TransportDataFixture::TRANSPORT_PERSONAL);
		$orderData->payment = $this->getReference(PaymentDataFixture::PAYMENT_CARD);
		$orderData->status = $this->getReference(OrderStatusDataFixture::ORDER_STATUS_NEW);
		$orderData->firstName = 'Iva';
		$orderData->lastName = 'Jačková';
		$orderData->email = 'no-reply@netdevelo.cz';
		$orderData->telephone = '+420367852147';
		$orderData->street = 'Druhá 2';
		$orderData->city = 'Ostrava';
		$orderData->postcode = '71300';
		$orderData->country = $this->getReference(CountryDataFixture::COUNTRY_SLOVAKIA_1);
		$orderData->deliveryAddressSameAsBillingAddress = true;
		$orderData->domainId = Domain::FIRST_DOMAIN_ID;
		$orderData->currency = $this->getReference(CurrencyDataFixture::CURRENCY_CZK);
		$this->createOrder(
			$orderData,
			[
				ProductDataFixture::PRODUCT_PREFIX . '18' => 2,
				ProductDataFixture::PRODUCT_PREFIX . '19' => 1,
				ProductDataFixture::PRODUCT_PREFIX . '20' => 1,
				ProductDataFixture::PRODUCT_PREFIX . '15' => 5,
			],
			$user
		);

		$orderData = new OrderData();
		$orderData->transport = $this->getReference(TransportDataFixture::TRANSPORT_CZECH_POST);
		$orderData->payment = $this->getReference(PaymentDataFixture::PAYMENT_CASH_ON_DELIVERY);
		$orderData->status = $this->getReference(OrderStatusDataFixture::ORDER_STATUS_NEW);
		$orderData->firstName = 'Jan';
		$orderData->lastName = 'Adamovský';
		$orderData->email = 'no-reply@netdevelo.cz';
		$orderData->telephone = '+420725852147';
		$orderData->street = 'Třetí 3';
		$orderData->city = 'Ostrava';
		$orderData->postcode = '71200';
		$orderData->country = $this->getReference(CountryDataFixture::COUNTRY_CZECH_REPUBLIC_1);
		$orderData->deliveryAddressSameAsBillingAddress = true;
		$orderData->domainId = Domain::FIRST_DOMAIN_ID;
		$orderData->currency = $this->getReference(CurrencyDataFixture::CURRENCY_CZK);
		$this->createOrder(
			$orderData,
			[
				ProductDataFixture::PRODUCT_PREFIX . '4' => 6,
				ProductDataFixture::PRODUCT_PREFIX . '11' => 1,
			],
			$user
		);

		$orderData = new OrderData();
		$orderData->transport = $this->getReference(TransportDataFixture::TRANSPORT_PPL);
		$orderData->payment = $this->getReference(PaymentDataFixture::PAYMENT_CARD);
		$orderData->status = $this->getReference(OrderStatusDataFixture::ORDER_STATUS_IN_PROGRESS);
		$orderData->firstName = 'Iveta';
		$orderData->lastName = 'Prvá';
		$orderData->email = 'no-reply@netdevelo.cz';
		$orderData->telephone = '+420606952147';
		$orderData->street = 'Čtvrtá 4';
		$orderData->city = 'Ostrava';
		$orderData->postcode = '70030';
		$orderData->country = $this->getReference(CountryDataFixture::COUNTRY_SLOVAKIA_1);
		$orderData->deliveryAddressSameAsBillingAddress = true;
		$orderData->domainId = Domain::FIRST_DOMAIN_ID;
		$orderData->currency = $this->getReference(CurrencyDataFixture::CURRENCY_CZK);
		$this->createOrder(
			$orderData,
			[
				ProductDataFixture::PRODUCT_PREFIX . '1' => 1,
			],
			$user
		);

		$orderData = new OrderData();
		$orderData->transport = $this->getReference(TransportDataFixture::TRANSPORT_PERSONAL);
		$orderData->payment = $this->getReference(PaymentDataFixture::PAYMENT_CASH);
		$orderData->status = $this->getReference(OrderStatusDataFixture::ORDER_STATUS_DONE);
		$orderData->firstName = 'Jana';
		$orderData->lastName = 'Janíčková';
		$orderData->email = 'no-reply@netdevelo.cz';
		$orderData->telephone = '+420739852148';
		$orderData->street = 'Pátá 55';
		$orderData->city = 'Ostrava';
		$orderData->postcode = '71200';
		$orderData->country = $this->getReference(CountryDataFixture::COUNTRY_CZECH_REPUBLIC_1);
		$orderData->deliveryAddressSameAsBillingAddress = true;
		$orderData->domainId = Domain::FIRST_DOMAIN_ID;
		$orderData->currency = $this->getReference(CurrencyDataFixture::CURRENCY_CZK);
		$this->createOrder(
			$orderData,
			[
				ProductDataFixture::PRODUCT_PREFIX . '2' => 8,
				ProductDataFixture::PRODUCT_PREFIX . '3' => 1,
				ProductDataFixture::PRODUCT_PREFIX . '1' => 2,
			],
			$user
		);

		$orderData = new OrderData();
		$orderData->transport = $this->getReference(TransportDataFixture::TRANSPORT_PPL);
		$orderData->payment = $this->getReference(PaymentDataFixture::PAYMENT_CARD);
		$orderData->status = $this->getReference(OrderStatusDataFixture::ORDER_STATUS_NEW);
		$orderData->firstName = 'Dominik';
		$orderData->lastName = 'Hašek';
		$orderData->email = 'no-reply@netdevelo.cz';
		$orderData->telephone = '+420721852152';
		$orderData->street = 'Šestá 39';
		$orderData->city = 'Pardubice';
		$orderData->postcode = '58941';
		$orderData->country = $this->getReference(CountryDataFixture::COUNTRY_SLOVAKIA_1);
		$orderData->deliveryAddressSameAsBillingAddress = true;
		$orderData->domainId = Domain::FIRST_DOMAIN_ID;
		$orderData->currency = $this->getReference(CurrencyDataFixture::CURRENCY_CZK);
		$this->createOrder(
			$orderData,
			[
				ProductDataFixture::PRODUCT_PREFIX . '13' => 2,
				ProductDataFixture::PRODUCT_PREFIX . '14' => 1,
				ProductDataFixture::PRODUCT_PREFIX . '15' => 1,
				ProductDataFixture::PRODUCT_PREFIX . '16' => 1,
				ProductDataFixture::PRODUCT_PREFIX . '17' => 1,
				ProductDataFixture::PRODUCT_PREFIX . '18' => 1,
			],
			$user
		);

		$orderData = new OrderData();
		$orderData->transport = $this->getReference(TransportDataFixture::TRANSPORT_PERSONAL);
		$orderData->payment = $this->getReference(PaymentDataFixture::PAYMENT_CASH);
		$orderData->status = $this->getReference(OrderStatusDataFixture::ORDER_STATUS_CANCELED);
		$orderData->firstName = 'Jiří';
		$orderData->lastName = 'Sovák';
		$orderData->email = 'no-reply@netdevelo.cz';
		$orderData->telephone = '+420755872155';
		$orderData->street = 'Sedmá 1488';
		$orderData->city = 'Opava';
		$orderData->postcode = '85741';
		$orderData->country = $this->getReference(CountryDataFixture::COUNTRY_CZECH_REPUBLIC_1);
		$orderData->deliveryAddressSameAsBillingAddress = true;
		$orderData->domainId = Domain::FIRST_DOMAIN_ID;
		$orderData->currency = $this->getReference(CurrencyDataFixture::CURRENCY_CZK);
		$this->createOrder(
			$orderData,
			[
				ProductDataFixture::PRODUCT_PREFIX . '7' => 1,
				ProductDataFixture::PRODUCT_PREFIX . '8' => 1,
				ProductDataFixture::PRODUCT_PREFIX . '12' => 2,
			]
		);

		$orderData = new OrderData();
		$orderData->transport = $this->getReference(TransportDataFixture::TRANSPORT_CZECH_POST);
		$orderData->payment = $this->getReference(PaymentDataFixture::PAYMENT_CASH_ON_DELIVERY);
		$orderData->status = $this->getReference(OrderStatusDataFixture::ORDER_STATUS_DONE);
		$orderData->firstName = 'Josef';
		$orderData->lastName = 'Somr';
		$orderData->email = 'no-reply@netdevelo.cz';
		$orderData->telephone = '+420369852147';
		$orderData->street = 'Osmá 1';
		$orderData->city = 'Praha';
		$orderData->postcode = '30258';
		$orderData->country = $this->getReference(CountryDataFixture::COUNTRY_SLOVAKIA_1);
		$orderData->deliveryAddressSameAsBillingAddress = true;
		$orderData->domainId = Domain::FIRST_DOMAIN_ID;
		$orderData->currency = $this->getReference(CurrencyDataFixture::CURRENCY_CZK);
		$this->createOrder(
			$orderData,
			[
				ProductDataFixture::PRODUCT_PREFIX . '1' => 6,
				ProductDataFixture::PRODUCT_PREFIX . '2' => 1,
				ProductDataFixture::PRODUCT_PREFIX . '12' => 1,
			]
		);

		$orderData = new OrderData();
		$orderData->transport = $this->getReference(TransportDataFixture::TRANSPORT_PERSONAL);
		$orderData->payment = $this->getReference(PaymentDataFixture::PAYMENT_CASH);
		$orderData->status = $this->getReference(OrderStatusDataFixture::ORDER_STATUS_CANCELED);
		$orderData->firstName = 'Ivan';
		$orderData->lastName = 'Horník';
		$orderData->email = 'no-reply@netdevelo.cz';
		$orderData->telephone = '+420755496328';
		$orderData->street = 'Desátá 10';
		$orderData->city = 'Plzeň';
		$orderData->postcode = '30010';
		$orderData->country = $this->getReference(CountryDataFixture::COUNTRY_CZECH_REPUBLIC_1);
		$orderData->deliveryAddressSameAsBillingAddress = true;
		$orderData->domainId = Domain::FIRST_DOMAIN_ID;
		$orderData->currency = $this->getReference(CurrencyDataFixture::CURRENCY_CZK);
		$this->createOrder(
			$orderData,
			[
				ProductDataFixture::PRODUCT_PREFIX . '9' => 3,
				ProductDataFixture::PRODUCT_PREFIX . '13' => 2,
			]
		);

		$orderData = new OrderData();
		$orderData->transport = $this->getReference(TransportDataFixture::TRANSPORT_PPL);
		$orderData->payment = $this->getReference(PaymentDataFixture::PAYMENT_CARD);
		$orderData->status = $this->getReference(OrderStatusDataFixture::ORDER_STATUS_NEW);
		$orderData->firstName = 'Adam';
		$orderData->lastName = 'Bořič';
		$orderData->email = 'no-reply@netdevelo.cz';
		$orderData->telephone = '+420987654321';
		$orderData->street = 'Cihelní 5';
		$orderData->city = 'Liberec';
		$orderData->postcode = '65421';
		$orderData->country = $this->getReference(CountryDataFixture::COUNTRY_CZECH_REPUBLIC_1);
		$orderData->deliveryAddressSameAsBillingAddress = true;
		$orderData->domainId = Domain::FIRST_DOMAIN_ID;
		$orderData->currency = $this->getReference(CurrencyDataFixture::CURRENCY_CZK);
		$this->createOrder(
			$orderData,
			[
				ProductDataFixture::PRODUCT_PREFIX . '3' => 1,
			]
		);

		$orderData = new OrderData();
		$orderData->transport = $this->getReference(TransportDataFixture::TRANSPORT_PERSONAL);
		$orderData->payment = $this->getReference(PaymentDataFixture::PAYMENT_CASH);
		$orderData->status = $this->getReference(OrderStatusDataFixture::ORDER_STATUS_IN_PROGRESS);
		$orderData->firstName = 'Evžen';
		$orderData->lastName = 'Farný';
		$orderData->email = 'no-reply@netdevelo.cz';
		$orderData->telephone = '+420456789123';
		$orderData->street = 'Gagarinova 333';
		$orderData->city = 'Hodonín';
		$orderData->postcode = '69501';
		$orderData->country = $this->getReference(CountryDataFixture::COUNTRY_SLOVAKIA_1);
		$orderData->deliveryAddressSameAsBillingAddress = true;
		$orderData->domainId = Domain::FIRST_DOMAIN_ID;
		$orderData->currency = $this->getReference(CurrencyDataFixture::CURRENCY_CZK);
		$this->createOrder(
			$orderData,
			[
				ProductDataFixture::PRODUCT_PREFIX . '1' => 1,
				ProductDataFixture::PRODUCT_PREFIX . '2' => 1,
				ProductDataFixture::PRODUCT_PREFIX . '3' => 1,
			]
		);

		$orderData = new OrderData();
		$orderData->transport = $this->getReference(TransportDataFixture::TRANSPORT_PERSONAL);
		$orderData->payment = $this->getReference(PaymentDataFixture::PAYMENT_CASH);
		$orderData->status = $this->getReference(OrderStatusDataFixture::ORDER_STATUS_DONE);
		$orderData->firstName = 'Ivana';
		$orderData->lastName = 'Janečková';
		$orderData->email = 'no-reply@netdevelo.cz';
		$orderData->telephone = '+420369852147';
		$orderData->street = 'Kalužní 88';
		$orderData->city = 'Lednice';
		$orderData->postcode = '69144';
		$orderData->country = $this->getReference(CountryDataFixture::COUNTRY_CZECH_REPUBLIC_1);
		$orderData->deliveryAddressSameAsBillingAddress = true;
		$orderData->domainId = Domain::FIRST_DOMAIN_ID;
		$orderData->currency = $this->getReference(CurrencyDataFixture::CURRENCY_CZK);
		$this->createOrder(
			$orderData,
			[
				ProductDataFixture::PRODUCT_PREFIX . '4' => 2,
				ProductDataFixture::PRODUCT_PREFIX . '3' => 1,
			]
		);

		$orderData = new OrderData();
		$orderData->transport = $this->getReference(TransportDataFixture::TRANSPORT_CZECH_POST);
		$orderData->payment = $this->getReference(PaymentDataFixture::PAYMENT_CASH_ON_DELIVERY);
		$orderData->status = $this->getReference(OrderStatusDataFixture::ORDER_STATUS_NEW);
		$orderData->firstName = 'Pavel';
		$orderData->lastName = 'Novák';
		$orderData->email = 'no-reply@netdevelo.cz';
		$orderData->telephone = '+420605123654';
		$orderData->street = 'Adresní 6';
		$orderData->city = 'Opava';
		$orderData->postcode = '72589';
		$orderData->country = $this->getReference(CountryDataFixture::COUNTRY_SLOVAKIA_1);
		$orderData->deliveryAddressSameAsBillingAddress = true;
		$orderData->domainId = Domain::FIRST_DOMAIN_ID;
		$orderData->currency = $this->getReference(CurrencyDataFixture::CURRENCY_CZK);
		$this->createOrder(
			$orderData,
			[
				ProductDataFixture::PRODUCT_PREFIX . '10' => 1,
				ProductDataFixture::PRODUCT_PREFIX . '20' => 4,
			]
		);

		$orderData = new OrderData();
		$orderData->transport = $this->getReference(TransportDataFixture::TRANSPORT_PPL);
		$orderData->payment = $this->getReference(PaymentDataFixture::PAYMENT_CARD);
		$orderData->status = $this->getReference(OrderStatusDataFixture::ORDER_STATUS_DONE);
		$orderData->firstName = 'Pavla';
		$orderData->lastName = 'Adámková';
		$orderData->email = 'no-reply@netdevelo.cz';
		$orderData->telephone = '+4206051836459';
		$orderData->street = 'Výpočetni 16';
		$orderData->city = 'Praha';
		$orderData->postcode = '30015';
		$orderData->country = $this->getReference(CountryDataFixture::COUNTRY_CZECH_REPUBLIC_1);
		$orderData->deliveryAddressSameAsBillingAddress = true;
		$orderData->domainId = Domain::FIRST_DOMAIN_ID;
		$orderData->currency = $this->getReference(CurrencyDataFixture::CURRENCY_CZK);
		$this->createOrder(
			$orderData,
			[
				ProductDataFixture::PRODUCT_PREFIX . '15' => 1,
				ProductDataFixture::PRODUCT_PREFIX . '18' => 1,
				ProductDataFixture::PRODUCT_PREFIX . '19' => 1,
				ProductDataFixture::PRODUCT_PREFIX . '3' => 1,
			]
		);

		$orderData = new OrderData();
		$orderData->transport = $this->getReference(TransportDataFixture::TRANSPORT_PERSONAL);
		$orderData->payment = $this->getReference(PaymentDataFixture::PAYMENT_CASH);
		$orderData->status = $this->getReference(OrderStatusDataFixture::ORDER_STATUS_IN_PROGRESS);
		$orderData->firstName = 'Adam';
		$orderData->lastName = 'Žitný';
		$orderData->email = 'no-reply@netdevelo.cz';
		$orderData->telephone = '+4206051836459';
		$orderData->street = 'Přímá 1';
		$orderData->city = 'Plzeň';
		$orderData->postcode = '30010';
		$orderData->country = $this->getReference(CountryDataFixture::COUNTRY_SLOVAKIA_1);
		$orderData->deliveryAddressSameAsBillingAddress = true;
		$orderData->domainId = Domain::FIRST_DOMAIN_ID;
		$orderData->currency = $this->getReference(CurrencyDataFixture::CURRENCY_CZK);
		$this->createOrder(
			$orderData,
			[
				ProductDataFixture::PRODUCT_PREFIX . '9' => 1,
				ProductDataFixture::PRODUCT_PREFIX . '19' => 1,
				ProductDataFixture::PRODUCT_PREFIX . '6' => 1,
			]
		);

		$orderData = new OrderData();
		$orderData->transport = $this->getReference(TransportDataFixture::TRANSPORT_PPL);
		$orderData->payment = $this->getReference(PaymentDataFixture::PAYMENT_CARD);
		$orderData->status = $this->getReference(OrderStatusDataFixture::ORDER_STATUS_NEW);
		$orderData->firstName = 'Radim';
		$orderData->lastName = 'Svátek';
		$orderData->email = 'no-reply@netdevelo.cz';
		$orderData->telephone = '+420733598748';
		$orderData->street = 'Křivá 11';
		$orderData->city = 'Jablonec';
		$orderData->postcode = '78952';
		$orderData->country = $this->getReference(CountryDataFixture::COUNTRY_CZECH_REPUBLIC_1);
		$orderData->deliveryAddressSameAsBillingAddress = true;
		$orderData->companyName = 'BestCompanyEver, s.r.o.';
		$orderData->companyNumber = '555555';
		$orderData->note = 'Doufám, že vše dorazí v pořádku a co nejdříve :)';
		$orderData->domainId = Domain::FIRST_DOMAIN_ID;
		$orderData->currency = $this->getReference(CurrencyDataFixture::CURRENCY_CZK);
		$this->createOrder(
			$orderData,
			[
				ProductDataFixture::PRODUCT_PREFIX . '7' => 1,
				ProductDataFixture::PRODUCT_PREFIX . '17' => 6,
				ProductDataFixture::PRODUCT_PREFIX . '9' => 1,
				ProductDataFixture::PRODUCT_PREFIX . '14' => 1,
				ProductDataFixture::PRODUCT_PREFIX . '10' => 2,
			]
		);
	}

	/**
	 * @param \Shopsys\ShopBundle\Model\Order\OrderData $orderData
	 * @param array $products
	 * @param \Shopsys\ShopBundle\Model\Customer\User $user
	 */
	private function createOrder(
		OrderData $orderData,
		array $products,
		User $user = null
	) {
		$orderFacade = $this->get(OrderFacade::class);
		/* @var $orderFacade \Shopsys\ShopBundle\Model\Order\OrderFacade */
		$orderPreviewFactory = $this->get(OrderPreviewFactory::class);
		/* @var $orderPreviewFactory \Shopsys\ShopBundle\Model\Order\Preview\OrderPreviewFactory */

		$quantifiedProducts = [];
		foreach ($products as $productReferenceName => $quantity) {
			$product = $this->getReference($productReferenceName);
			$quantifiedProducts[] = new QuantifiedProduct($product, $quantity);
		}
		$orderPreview = $orderPreviewFactory->create(
			$orderData->currency,
			$orderData->domainId,
			$quantifiedProducts,
			$orderData->transport,
			$orderData->payment,
			$user,
			null
		);

		$order = $orderFacade->createOrder($orderData, $orderPreview, $user);
		/* @var $order \Shopsys\ShopBundle\Model\Order\Order */

		$referenceName = self::ORDER_PREFIX . $order->getId();
		$this->addReference($referenceName, $order);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getDependencies() {
		return [
			ProductDataFixture::class,
			TransportDataFixture::class,
			PaymentDataFixture::class,
			UserDataFixture::class,
			OrderStatusDataFixture::class,
			CountryDataFixture::class,
		];
	}

}
