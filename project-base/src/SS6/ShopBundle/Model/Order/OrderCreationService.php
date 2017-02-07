<?php

namespace SS6\ShopBundle\Model\Order;

use SS6\ShopBundle\Component\Domain\Domain;
use SS6\ShopBundle\Model\Customer\User;
use SS6\ShopBundle\Model\Order\FrontOrderData;
use SS6\ShopBundle\Model\Order\Item\OrderItem;
use SS6\ShopBundle\Model\Order\Item\OrderItemPriceCalculation;
use SS6\ShopBundle\Model\Order\Item\OrderPayment;
use SS6\ShopBundle\Model\Order\Item\OrderProduct;
use SS6\ShopBundle\Model\Order\Item\OrderTransport;
use SS6\ShopBundle\Model\Order\Order;
use SS6\ShopBundle\Model\Order\OrderPriceCalculation;
use SS6\ShopBundle\Model\Order\Preview\OrderPreview;
use SS6\ShopBundle\Model\Payment\PaymentPriceCalculation;
use SS6\ShopBundle\Model\Pricing\Price;
use SS6\ShopBundle\Model\Product\Pricing\ProductPriceCalculationForUser;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Transport\TransportPriceCalculation;
use SS6\ShopBundle\Twig\NumberFormatterExtension;

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
	 * @var \SS6\ShopBundle\Component\Domain\Domain
	 */
	private $domain;

	/**
	 * @var \SS6\ShopBundle\Twig\NumberFormatterExtension
	 */
	private $numberFormatterExtension;

	public function __construct(
		OrderItemPriceCalculation $orderItemPriceCalculation,
		OrderPriceCalculation $orderPriceCalculation,
		ProductPriceCalculationForUser $productPriceCalculationForUser,
		PaymentPriceCalculation $paymentPriceCalculation,
		TransportPriceCalculation $transportPriceCalculation,
		Domain $domain,
		NumberFormatterExtension $numberFormatterExtension
	) {
		$this->orderItemPriceCalculation = $orderItemPriceCalculation;
		$this->orderPriceCalculation = $orderPriceCalculation;
		$this->productPriceCalculationForUser = $productPriceCalculationForUser;
		$this->paymentPriceCalculation = $paymentPriceCalculation;
		$this->transportPriceCalculation = $transportPriceCalculation;
		$this->domain = $domain;
		$this->numberFormatterExtension = $numberFormatterExtension;
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
		$frontOrderData->country = $user->getBillingAddress()->getCountry();
		if ($user->getDeliveryAddress() !== null) {
			$frontOrderData->deliveryAddressSameAsBillingAddress = false;
			$frontOrderData->deliveryFirstName = $user->getDeliveryAddress()->getFirstName();
			$frontOrderData->deliveryLastName = $user->getDeliveryAddress()->getLastName();
			$frontOrderData->deliveryCompanyName = $user->getDeliveryAddress()->getCompanyName();
			$frontOrderData->deliveryTelephone = $user->getDeliveryAddress()->getTelephone();
			$frontOrderData->deliveryStreet = $user->getDeliveryAddress()->getStreet();
			$frontOrderData->deliveryCity = $user->getDeliveryAddress()->getCity();
			$frontOrderData->deliveryPostcode = $user->getDeliveryAddress()->getPostcode();
			$frontOrderData->deliveryCountry = $user->getDeliveryAddress()->getCountry();
		} else {
			$frontOrderData->deliveryAddressSameAsBillingAddress = true;
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
		$this->fillOrderRounding($order, $orderPreview, $locale);
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
			$paymentPrice,
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
			$transportPrice,
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
		$quantifiedItemDiscounts = $orderPreview->getQuantifiedItemsDiscounts();

		foreach ($orderPreview->getQuantifiedProducts() as $index => $quantifiedProduct) {
			$product = $quantifiedProduct->getProduct();
			if (!$product instanceof Product) {
				$message = 'Object "' . get_class($product) . '" is not valid for order creation.';
				throw new \SS6\ShopBundle\Model\Order\Item\Exception\InvalidQuantifiedProductException($message);
			}

			$quantifiedItemPrice = $quantifiedItemPrices[$index];
			/* @var $quantifiedItemPrice \SS6\ShopBundle\Model\Order\Item\QuantifiedItemPrice */
			$quantifiedItemDiscount = $quantifiedItemDiscounts[$index];
			/* @var $quantifiedItemDiscount \SS6\ShopBundle\Model\Pricing\Price|null */

			$orderItem = new OrderProduct(
				$order,
				$product->getName($locale),
				$quantifiedItemPrice->getUnitPrice(),
				$product->getVat()->getPercent(),
				$quantifiedProduct->getQuantity(),
				$product->getUnit()->getName($locale),
				$product->getCatnum(),
				$product
			);

			if ($quantifiedItemDiscount !== null) {
				$this->addOrderItemDiscount($orderItem, $quantifiedItemDiscount, $locale, $orderPreview->getPromoCodeDiscountPercent());
			}
		}
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Order $order
	 * @param \SS6\ShopBundle\Model\Order\Preview\OrderPreview $orderPreview
	 * @param string $locale
	 */
	private function fillOrderRounding(Order $order, OrderPreview $orderPreview, $locale) {
		if ($orderPreview->getRoundingPrice() !== null) {
			new OrderProduct(
				$order,
				t('Rounding', [], 'messages', $locale),
				$orderPreview->getRoundingPrice(),
				0,
				1,
				null,
				null,
				null
			);
		}
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Item\OrderItem $orderItem
	 * @param \SS6\ShopBundle\Model\Pricing\Price $discount
	 * @param string $locale
	 * @param float $discountPercent
	 */
	private function addOrderItemDiscount(OrderItem $orderItem, Price $discount, $locale, $discountPercent) {
		$name = sprintf('%s %s - %s',
			t('Discount coupon', [], 'messages', $locale),
			$this->numberFormatterExtension->formatPercent(-$discountPercent, $locale),
			$orderItem->getName()
		);

		new OrderProduct(
			$orderItem->getOrder(),
			$name,
			new Price(
				-$discount->getPriceWithoutVat(),
				-$discount->getPriceWithVat()
			),
			$orderItem->getVatPercent(),
			1,
			null,
			null,
			null
		);
	}

}
