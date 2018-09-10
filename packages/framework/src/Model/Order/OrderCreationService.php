<?php

namespace Shopsys\FrameworkBundle\Model\Order;

use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Model\Customer\User;
use Shopsys\FrameworkBundle\Model\Order\Item\OrderItem;
use Shopsys\FrameworkBundle\Model\Order\Item\OrderPaymentFactoryInterface;
use Shopsys\FrameworkBundle\Model\Order\Item\OrderProductFactoryInterface;
use Shopsys\FrameworkBundle\Model\Order\Item\OrderTransportFactoryInterface;
use Shopsys\FrameworkBundle\Model\Order\Preview\OrderPreview;
use Shopsys\FrameworkBundle\Model\Payment\PaymentPriceCalculation;
use Shopsys\FrameworkBundle\Model\Pricing\Price;
use Shopsys\FrameworkBundle\Model\Product\Product;
use Shopsys\FrameworkBundle\Model\Transport\TransportPriceCalculation;
use Shopsys\FrameworkBundle\Twig\NumberFormatterExtension;

class OrderCreationService
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Payment\PaymentPriceCalculation
     */
    private $paymentPriceCalculation;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Transport\TransportPriceCalculation
     */
    private $transportPriceCalculation;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @var \Shopsys\FrameworkBundle\Twig\NumberFormatterExtension
     */
    private $numberFormatterExtension;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\Item\OrderPaymentFactoryInterface
     */
    protected $orderPaymentFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\Item\OrderProductFactoryInterface
     */
    protected $orderProductFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\Item\OrderTransportFactoryInterface
     */
    protected $orderTransportFactory;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Payment\PaymentPriceCalculation $paymentPriceCalculation
     * @param \Shopsys\FrameworkBundle\Model\Transport\TransportPriceCalculation $transportPriceCalculation
     * @param \Shopsys\FrameworkBundle\Component\Domain\Domain $domain
     * @param \Shopsys\FrameworkBundle\Twig\NumberFormatterExtension $numberFormatterExtension
     * @param \Shopsys\FrameworkBundle\Model\Order\Item\OrderPaymentFactoryInterface $orderPaymentFactory
     * @param \Shopsys\FrameworkBundle\Model\Order\Item\OrderProductFactoryInterface $orderProductFactory
     * @param \Shopsys\FrameworkBundle\Model\Order\Item\OrderTransportFactoryInterface $orderTransportFactory
     */
    public function __construct(
        PaymentPriceCalculation $paymentPriceCalculation,
        TransportPriceCalculation $transportPriceCalculation,
        Domain $domain,
        NumberFormatterExtension $numberFormatterExtension,
        OrderPaymentFactoryInterface $orderPaymentFactory,
        OrderProductFactoryInterface $orderProductFactory,
        OrderTransportFactoryInterface $orderTransportFactory
    ) {
        $this->paymentPriceCalculation = $paymentPriceCalculation;
        $this->transportPriceCalculation = $transportPriceCalculation;
        $this->domain = $domain;
        $this->numberFormatterExtension = $numberFormatterExtension;
        $this->orderPaymentFactory = $orderPaymentFactory;
        $this->orderProductFactory = $orderProductFactory;
        $this->orderTransportFactory = $orderTransportFactory;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\FrontOrderData $frontOrderData
     * @param \Shopsys\FrameworkBundle\Model\Customer\User $user
     * @param \Shopsys\FrameworkBundle\Model\Order\Order $order
     */
    public function prefillFrontFormData(FrontOrderData $frontOrderData, User $user, Order $order = null)
    {
        if ($order instanceof Order) {
            $this->prefillTransportAndPaymentFromOrder($frontOrderData, $order);
        }
        $this->prefillFrontFormDataFromCustomer($frontOrderData, $user);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\FrontOrderData $frontOrderData
     * @param \Shopsys\FrameworkBundle\Model\Order\Order $order
     */
    private function prefillTransportAndPaymentFromOrder(FrontOrderData $frontOrderData, Order $order)
    {
        $frontOrderData->transport = $order->getTransport();
        $frontOrderData->payment = $order->getPayment();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\FrontOrderData $frontOrderData
     * @param \Shopsys\FrameworkBundle\Model\Customer\User $user
     */
    private function prefillFrontFormDataFromCustomer(FrontOrderData $frontOrderData, User $user)
    {
        $frontOrderData->firstName = $user->getFirstName();
        $frontOrderData->lastName = $user->getLastName();
        $frontOrderData->email = $user->getEmail();
        $frontOrderData->telephone = $user->getTelephone();
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
     * @param \Shopsys\FrameworkBundle\Model\Order\Order $order
     * @param \Shopsys\FrameworkBundle\Model\Order\Preview\OrderPreview $orderPreview
     */
    public function fillOrderItems(Order $order, OrderPreview $orderPreview)
    {
        $locale = $this->domain->getDomainConfigById($order->getDomainId())->getLocale();

        $this->fillOrderProducts($order, $orderPreview, $locale);
        $this->fillOrderTransportAndPayment($order, $orderPreview, $locale);
        $this->fillOrderRounding($order, $orderPreview, $locale);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Order $order
     * @param \Shopsys\FrameworkBundle\Model\Order\Preview\OrderPreview $orderPreview
     * @param string $locale
     */
    private function fillOrderTransportAndPayment(Order $order, OrderPreview $orderPreview, $locale)
    {
        $payment = $order->getPayment();
        $paymentPrice = $this->paymentPriceCalculation->calculatePrice(
            $payment,
            $order->getCurrency(),
            $orderPreview->getProductsPrice(),
            $order->getDomainId()
        );
        $orderPayment = $this->orderPaymentFactory->create(
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
        $orderTransport = $this->orderTransportFactory->create(
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
     * @param \Shopsys\FrameworkBundle\Model\Order\Order $order
     * @param \Shopsys\FrameworkBundle\Model\Order\Preview\OrderPreview $orderPreview
     * @param string $locale
     */
    private function fillOrderProducts(Order $order, OrderPreview $orderPreview, $locale)
    {
        $quantifiedItemPrices = $orderPreview->getQuantifiedItemsPrices();
        $quantifiedItemDiscounts = $orderPreview->getQuantifiedItemsDiscounts();

        foreach ($orderPreview->getQuantifiedProducts() as $index => $quantifiedProduct) {
            $product = $quantifiedProduct->getProduct();
            if (!$product instanceof Product) {
                $message = 'Object "' . get_class($product) . '" is not valid for order creation.';
                throw new \Shopsys\FrameworkBundle\Model\Order\Item\Exception\InvalidQuantifiedProductException($message);
            }

            $quantifiedItemPrice = $quantifiedItemPrices[$index];
            /* @var $quantifiedItemPrice \Shopsys\FrameworkBundle\Model\Order\Item\QuantifiedItemPrice */
            $quantifiedItemDiscount = $quantifiedItemDiscounts[$index];
            /* @var $quantifiedItemDiscount \Shopsys\FrameworkBundle\Model\Pricing\Price|null */

            $orderItem = $this->orderProductFactory->create(
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
     * @param \Shopsys\FrameworkBundle\Model\Order\Order $order
     * @param \Shopsys\FrameworkBundle\Model\Order\Preview\OrderPreview $orderPreview
     * @param string $locale
     */
    private function fillOrderRounding(Order $order, OrderPreview $orderPreview, $locale)
    {
        if ($orderPreview->getRoundingPrice() !== null) {
            $this->orderProductFactory->create(
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
     * @param \Shopsys\FrameworkBundle\Model\Order\Item\OrderItem $orderItem
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Price $discount
     * @param string $locale
     * @param float $discountPercent
     */
    private function addOrderItemDiscount(OrderItem $orderItem, Price $discount, $locale, $discountPercent)
    {
        $name = sprintf(
            '%s %s - %s',
            t('Promo code', [], 'messages', $locale),
            $this->numberFormatterExtension->formatPercent(-$discountPercent, $locale),
            $orderItem->getName()
        );

        $this->orderProductFactory->create(
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
