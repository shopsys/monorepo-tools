<?php

namespace Shopsys\FrameworkBundle\Model\Order;

use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Model\Order\Item\OrderPaymentFactoryInterface;
use Shopsys\FrameworkBundle\Model\Order\Item\OrderProductFactoryInterface;
use Shopsys\FrameworkBundle\Model\Order\Item\OrderTransportFactoryInterface;
use Shopsys\FrameworkBundle\Model\Order\Preview\OrderPreview;
use Shopsys\FrameworkBundle\Model\Payment\PaymentPriceCalculation;
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
     * @param \Shopsys\FrameworkBundle\Model\Order\Order $order
     * @param \Shopsys\FrameworkBundle\Model\Order\Preview\OrderPreview $orderPreview
     */
    public function fillOrderItems(Order $order, OrderPreview $orderPreview)
    {
        $locale = $this->domain->getDomainConfigById($order->getDomainId())->getLocale();

        $order->fillOrderProducts($orderPreview, $this->orderProductFactory, $this->numberFormatterExtension, $locale);
        $order->fillOrderPayment($this->paymentPriceCalculation, $this->orderPaymentFactory, $orderPreview->getProductsPrice(), $locale);
        $order->fillOrderTransport($this->transportPriceCalculation, $this->orderTransportFactory, $orderPreview->getProductsPrice(), $locale);
        $order->fillOrderRounding($this->orderProductFactory, $orderPreview->getRoundingPrice(), $locale);
    }
}
