<?php

namespace Shopsys\FrameworkBundle\Model\Order\Mail;

use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\Router\DomainRouterFactory;
use Shopsys\FrameworkBundle\Component\Setting\Setting;
use Shopsys\FrameworkBundle\Model\Mail\MailTemplate;
use Shopsys\FrameworkBundle\Model\Mail\MessageData;
use Shopsys\FrameworkBundle\Model\Mail\Setting\MailSetting;
use Shopsys\FrameworkBundle\Model\Order\Item\OrderItemPriceCalculation;
use Shopsys\FrameworkBundle\Model\Order\Order;
use Shopsys\FrameworkBundle\Model\Order\OrderService;
use Shopsys\FrameworkBundle\Model\Order\Status\OrderStatus;
use Shopsys\FrameworkBundle\Twig\DateTimeFormatterExtension;
use Shopsys\FrameworkBundle\Twig\PriceExtension;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig_Environment;

class OrderMailService
{
    const MAIL_TEMPLATE_NAME_PREFIX = 'order_status_';
    const VARIABLE_NUMBER = '{number}';
    const VARIABLE_DATE = '{date}';
    const VARIABLE_URL = '{url}';
    const VARIABLE_TRANSPORT = '{transport}';
    const VARIABLE_PAYMENT = '{payment}';
    const VARIABLE_TOTAL_PRICE = '{total_price}';
    const VARIABLE_BILLING_ADDRESS = '{billing_address}';
    const VARIABLE_DELIVERY_ADDRESS = '{delivery_address}';
    const VARIABLE_NOTE = '{note}';
    const VARIABLE_PRODUCTS = '{products}';
    const VARIABLE_ORDER_DETAIL_URL = '{order_detail_url}';
    const VARIABLE_TRANSPORT_INSTRUCTIONS = '{transport_instructions}';
    const VARIABLE_PAYMENT_INSTRUCTIONS = '{payment_instructions}';

    /**
     * @var \Shopsys\FrameworkBundle\Component\Setting\Setting
     */
    private $setting;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Router\DomainRouterFactory
     */
    private $domainRouterFactory;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\Item\OrderItemPriceCalculation
     */
    private $orderItemPriceCalculation;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @var \Shopsys\FrameworkBundle\Twig\PriceExtension
     */
    private $priceExtension;

    /**
     * @var \Shopsys\FrameworkBundle\Twig\DateTimeFormatterExtension
     */
    private $dateTimeFormatterExtension;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\OrderService
     */
    private $orderService;

    /**
     * @param \Shopsys\FrameworkBundle\Component\Setting\Setting $setting
     * @param \Shopsys\FrameworkBundle\Component\Router\DomainRouterFactory $domainRouterFactory
     * @param \Twig_Environment $twig
     * @param \Shopsys\FrameworkBundle\Model\Order\Item\OrderItemPriceCalculation $orderItemPriceCalculation
     * @param \Shopsys\FrameworkBundle\Component\Domain\Domain $domain
     * @param \Shopsys\FrameworkBundle\Twig\PriceExtension $priceExtension
     * @param \Shopsys\FrameworkBundle\Twig\DateTimeFormatterExtension $dateTimeFormatterExtension
     * @param \Shopsys\FrameworkBundle\Model\Order\OrderService $orderService
     */
    public function __construct(
        Setting $setting,
        DomainRouterFactory $domainRouterFactory,
        Twig_Environment $twig,
        OrderItemPriceCalculation $orderItemPriceCalculation,
        Domain $domain,
        PriceExtension $priceExtension,
        DateTimeFormatterExtension $dateTimeFormatterExtension,
        OrderService $orderService
    ) {
        $this->setting = $setting;
        $this->domainRouterFactory = $domainRouterFactory;
        $this->twig = $twig;
        $this->orderItemPriceCalculation = $orderItemPriceCalculation;
        $this->domain = $domain;
        $this->priceExtension = $priceExtension;
        $this->dateTimeFormatterExtension = $dateTimeFormatterExtension;
        $this->orderService = $orderService;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Order $order
     * @param \Shopsys\FrameworkBundle\Model\Mail\MailTemplate $mailTemplate
     * @return \Shopsys\FrameworkBundle\Model\Mail\MessageData
     */
    public function getMessageDataByOrder(Order $order, MailTemplate $mailTemplate)
    {
        return new MessageData(
            $order->getEmail(),
            $mailTemplate->getBccEmail(),
            $mailTemplate->getBody(),
            $mailTemplate->getSubject(),
            $this->setting->getForDomain(MailSetting::MAIN_ADMIN_MAIL, $order->getDomainId()),
            $this->setting->getForDomain(MailSetting::MAIN_ADMIN_MAIL_NAME, $order->getDomainId()),
            $this->getVariablesReplacementsForBody($order),
            $this->getVariablesReplacementsForSubject($order)
        );
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Status\OrderStatus $orderStatus
     * @return string
     */
    public function getMailTemplateNameByStatus(OrderStatus $orderStatus)
    {
        return self::MAIL_TEMPLATE_NAME_PREFIX . $orderStatus->getId();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Order $order
     * @return array
     */
    private function getVariablesReplacementsForBody(Order $order)
    {
        $router = $this->domainRouterFactory->getRouter($order->getDomainId());
        $orderDomainConfig = $this->domain->getDomainConfigById($order->getDomainId());

        $transport = $order->getTransport();
        $payment = $order->getPayment();

        $transportInstructions = $transport->getInstructions($orderDomainConfig->getLocale());
        $paymentInstructions = $payment->getInstructions($orderDomainConfig->getLocale());

        return [
            self::VARIABLE_NUMBER => $order->getNumber(),
            self::VARIABLE_DATE => $this->getFormattedDateTime($order),
            self::VARIABLE_URL => $router->generate('front_homepage', [], UrlGeneratorInterface::ABSOLUTE_URL),
            self::VARIABLE_TRANSPORT => $order->getTransportName(),
            self::VARIABLE_PAYMENT => $order->getPaymentName(),
            self::VARIABLE_TOTAL_PRICE => $this->getFormattedPrice($order),
            self::VARIABLE_BILLING_ADDRESS => $this->getBillingAddressHtmlTable($order),
            self::VARIABLE_DELIVERY_ADDRESS => $this->getDeliveryAddressHtmlTable($order),
            self::VARIABLE_NOTE => $order->getNote(),
            self::VARIABLE_PRODUCTS => $this->getProductsHtmlTable($order),
            self::VARIABLE_ORDER_DETAIL_URL => $this->orderService->getOrderDetailUrl($order),
            self::VARIABLE_TRANSPORT_INSTRUCTIONS => $transportInstructions,
            self::VARIABLE_PAYMENT_INSTRUCTIONS => $paymentInstructions,
        ];
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Order $order
     * @return array
     */
    private function getVariablesReplacementsForSubject(Order $order)
    {
        return [
            self::VARIABLE_NUMBER => $order->getNumber(),
            self::VARIABLE_DATE => $this->getFormattedDateTime($order),
        ];
    }

    /**
     * @return array
     */
    public function getTemplateVariables()
    {
        return [
            self::VARIABLE_NUMBER,
            self::VARIABLE_DATE,
            self::VARIABLE_URL,
            self::VARIABLE_TRANSPORT,
            self::VARIABLE_PAYMENT,
            self::VARIABLE_TOTAL_PRICE,
            self::VARIABLE_BILLING_ADDRESS,
            self::VARIABLE_DELIVERY_ADDRESS,
            self::VARIABLE_NOTE,
            self::VARIABLE_PRODUCTS,
            self::VARIABLE_ORDER_DETAIL_URL,
            self::VARIABLE_TRANSPORT_INSTRUCTIONS,
            self::VARIABLE_PAYMENT_INSTRUCTIONS,
        ];
    }

    /**
     * @param  \Shopsys\FrameworkBundle\Model\Order\Order $order
     * @return string
     */
    private function getFormattedPrice(Order $order)
    {
        return $this->priceExtension->priceTextWithCurrencyByCurrencyIdAndLocaleFilter(
            $order->getTotalPriceWithVat(),
            $order->getCurrency()->getId(),
            $this->getDomainLocaleByOrder($order)
        );
    }

    /**
     * @param  \Shopsys\FrameworkBundle\Model\Order\Order $order
     * @return string
     */
    private function getFormattedDateTime(Order $order)
    {
        return $this->dateTimeFormatterExtension->formatDateTime(
            $order->getCreatedAt(),
            $this->getDomainLocaleByOrder($order)
        );
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Order $order
     * @return string
     */
    private function getBillingAddressHtmlTable(Order $order)
    {
        return $this->twig->render('@ShopsysFramework/Mail/Order/billingAddress.html.twig', [
            'order' => $order,
            'orderLocale' => $this->getDomainLocaleByOrder($order),
        ]);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Order $order
     * @return string
     */
    private function getDeliveryAddressHtmlTable(Order $order)
    {
        return $this->twig->render('@ShopsysFramework/Mail/Order/deliveryAddress.html.twig', [
            'order' => $order,
            'orderLocale' => $this->getDomainLocaleByOrder($order),
        ]);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Order $order
     * @return string
     */
    private function getProductsHtmlTable(Order $order)
    {
        $orderItemTotalPricesById = $this->orderItemPriceCalculation->calculateTotalPricesIndexedById($order->getItems());

        return $this->twig->render('@ShopsysFramework/Mail/Order/products.html.twig', [
            'order' => $order,
            'orderItemTotalPricesById' => $orderItemTotalPricesById,
            'orderLocale' => $this->getDomainLocaleByOrder($order),
        ]);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Order $order
     * @return string
     */
    private function getDomainLocaleByOrder(Order $order)
    {
        return $this->domain->getDomainConfigById($order->getDomainId())->getLocale();
    }
}
