<?php

namespace SS6\ShopBundle\Model\Order;

use SS6\ShopBundle\Model\Customer\User;
use SS6\ShopBundle\Model\Domain\Domain;
use SS6\ShopBundle\Model\Order\FrontOrderData;
use SS6\ShopBundle\Model\Order\Item\OrderItemPriceCalculation;
use SS6\ShopBundle\Model\Order\Item\OrderPayment;
use SS6\ShopBundle\Model\Order\Item\OrderProduct;
use SS6\ShopBundle\Model\Order\Item\OrderTransport;
use SS6\ShopBundle\Model\Order\Order;
use SS6\ShopBundle\Model\Order\OrderPriceCalculation;
use SS6\ShopBundle\Model\Order\Preview\OrderPreview;
use SS6\ShopBundle\Model\Payment\PaymentPriceCalculation;
use SS6\ShopBundle\Model\Product\Pricing\ProductPriceCalculationForUser;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Transport\TransportPriceCalculation;

class OrderCreationService {

	/**
	 * @var \SS6\ShopBundle\Model\Order\Item\OrderItemPriceCalculation
	 */
	private $orderItemPriceCalculation;

	/**
	 * @var \SS6\ShopBundle\Model\Order\OrderPriceCalculation
	 */
	private $orderPriceCalculation;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Pricing\ProductPriceCalculationForUser
	 */
	private $productPriceCalculationForUser;

	/**
	 * @var \SS6\ShopBundle\Model\Payment\PaymentPriceCalculation
	 */
	private $paymentPriceCalculation;

	/**
	 * @var \SS6\ShopBundle\Model\Transport\TransportPriceCalculation
	 */
	private $transportPriceCalculation;

	/**
	 * @var \SS6\ShopBundle\Model\Domain\Domain
	 */
	private $domain;

	public function __construct(
		OrderItemPriceCalculation $orderItemPriceCalculation,
		OrderPriceCalculation $orderPriceCalculation,
		ProductPriceCalculationForUser $productPriceCalculationForUser,
		PaymentPriceCalculation $paymentPriceCalculation,
		TransportPriceCalculation $transportPriceCalculation,
		Domain $domain
	) {
		$this->orderItemPriceCalculation = $orderItemPriceCalculation;
		$this->orderPriceCalculation = $orderPriceCalculation;
		$this->productPriceCalculationForUser = $productPriceCalculationForUser;
		$this->paymentPriceCalculation = $paymentPriceCalculation;
		$this->transportPriceCalculation = $transportPriceCalculation;
		$this->domain = $domain;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\FrontOrderData $frontOrderData
	 * @param \SS6\ShopBundle\Model\Customer\User $user
	 * @param \SS6\ShopBundle\Model\Order\Order $order
	 */
	public function prefillFrontFormData(FrontOrderData $frontOrderData, User $user, Order $order = null) {
		if ($order instanceof Order) {
			$this->prefillTransportAndPaymentFromOrder($frontOrderData, $order);
		}
		$this->prefillFrontFormDataFromCustomer($frontOrderData, $user);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\FrontOrderData $frontOrderData
	 * @param \SS6\ShopBundle\Model\Order\Order $order
	 */
	private function prefillTransportAndPaymentFromOrder(FrontOrderData $frontOrderData, Order $order) {
		$frontOrderData->transport = $order->getTransport();
		$frontOrderData->payment = $order->getPayment();
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\FrontOrderData $frontOrderData
	 * @param \SS6\ShopBundle\Model\Customer\User $user
	 */
	private function prefillFrontFormDataFromCustomer(FrontOrderData $frontOrderData, User $user) {
		$frontOrderData->firstName = $user->getFirstName();
		$frontOrderData->lastName = $user->getLastName();
		$frontOrderData->email = $user->getEmail();
		$frontOrderData->telephone = $user->getBillingAddress()->getTelephone();
		$frontOrderData->companyCustomer = $user->getBillingAddress()->isCompanyCustomer();
		$frontOrderData->companyName = $user->getBillingAddress()->getCompanyName();
		$frontOrderData->companyNumber = $user->getBillingAddress()->getCompanyNumber();
		$frontOrderData->companyTaxNumber = $user->getBillingAddress()->getCompanyTaxNumber();
		$frontOrderData->street = $user->getBillingAddress()->getStreet();
		$frontOrderData->city = $user->getBillingAddress()->getCity();
		$frontOrderData->postcode = $user->getBillingAddress()->getPostcode();
		if ($user->getDeliveryAddress() !== null) {
			$frontOrderData->deliveryAddressFilled = true;
			$frontOrderData->deliveryContactPerson = $user->getDeliveryAddress()->getContactPerson();
			$frontOrderData->deliveryCompanyName = $user->getDeliveryAddress()->getCompanyName();
			$frontOrderData->deliveryTelephone = $user->getDeliveryAddress()->getTelephone();
			$frontOrderData->deliveryStreet = $user->getDeliveryAddress()->getStreet();
			$frontOrderData->deliveryCity = $user->getDeliveryAddress()->getCity();
			$frontOrderData->deliveryPostcode = $user->getDeliveryAddress()->getPostcode();
		} else {
			$frontOrderData->deliveryAddressFilled = false;
		}
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Order $order
	 * @param \SS6\ShopBundle\Model\Order\Preview\OrderPreview $orderPreview
	 */
	public function fillOrderItems(Order $order, OrderPreview $orderPreview) {
		$locale = $this->domain->getDomainConfigById($order->getDomainId())->getLocale();

		$this->fillOrderProducts($order, $orderPreview, $locale);
		$this->fillOrderTransportAndPayment($order, $orderPreview, $locale);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Order $order
	 * @param \SS6\ShopBundle\Model\Order\Preview\OrderPreview $orderPreview
	 * @param string $locale
	 */
	private function fillOrderTransportAndPayment(Order $order, OrderPreview $orderPreview, $locale) {
		$payment = $order->getPayment();
		$paymentPrice = $this->paymentPriceCalculation->calculatePrice(
			$payment,
			$order->getCurrency(),
			$orderPreview->getProductsPrice(),
			$order->getDomainId()
		);
		$orderPayment = new OrderPayment(
			$order,
			$payment->getName($locale),
			$paymentPrice->getPriceWithoutVat(),
			$paymentPrice->getPriceWithVat(),
			$payment->getVat()->getPercent(),
			1,
			$payment
		);
		$order->addItem($orderPayment);

		$transport = $order->getTransport();
		$transportPrice = $this->transportPriceCalculation->calculatePrice(
			$transport,
			$order->getCurrency(),
			$orderPreview->getProductsPrice(),
			$order->getDomainId()
		);
		$orderTransport = new OrderTransport(
			$order,
			$transport->getName($locale),
			$transportPrice->getPriceWithoutVat(),
			$transportPrice->getPriceWithVat(),
			$transport->getVat()->getPercent(),
			1,
			$transport
		);
		$order->addItem($orderTransport);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Order $order
	 * @param \SS6\ShopBundle\Model\Order\Preview\OrderPreview $orderPreview
	 * @param string $locale
	 */
	private function fillOrderProducts(Order $order, OrderPreview $orderPreview, $locale) {
		$quantifiedItemPrices = $orderPreview->getQuantifiedItemsPrices();

		foreach ($orderPreview->getQuantifiedItems() as $index => $quantifiedItem) {
			$product = $quantifiedItem->getItem();
			if (!$product instanceof Product) {
				$message = 'Object "' . get_class($product) . '" is not valid for order creation.';
				throw new \SS6\ShopBundle\Model\Order\Item\Exception\InvalidQuantifiedItemException($message);
			}

			$quantifiedItemPrice = $quantifiedItemPrices[$index];
			/* @var $quantifiedItemPrice \SS6\ShopBundle\Model\Order\Item\QuantifiedItemPrice */

			$orderItem = new OrderProduct(
				$order,
				$product->getName($locale),
				$quantifiedItemPrice->getUnitPriceWithoutVat(),
				$quantifiedItemPrice->getUnitPriceWithVat(),
				$product->getVat()->getPercent(),
				$quantifiedItem->getQuantity(),
				$product->getCatnum(),
				$product
			);

			$order->addItem($orderItem);
		}
	}

}
