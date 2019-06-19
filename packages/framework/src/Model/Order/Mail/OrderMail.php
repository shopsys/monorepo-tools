<?php

namespace Shopsys\FrameworkBundle\Model\Order\Mail;

use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\Router\DomainRouterFactory;
use Shopsys\FrameworkBundle\Component\Setting\Setting;
use Shopsys\FrameworkBundle\Model\Mail\MailTemplate;
use Shopsys\FrameworkBundle\Model\Mail\MessageData;
use Shopsys\FrameworkBundle\Model\Mail\MessageFactoryInterface;
use Shopsys\FrameworkBundle\Model\Mail\Setting\MailSetting;
use Shopsys\FrameworkBundle\Model\Order\Item\OrderItemPriceCalculation;
use Shopsys\FrameworkBundle\Model\Order\Order;
use Shopsys\FrameworkBundle\Model\Order\OrderUrlGenerator;
use Shopsys\FrameworkBundle\Model\Order\Status\OrderStatus;
use Shopsys\FrameworkBundle\Twig\DateTimeFormatterExtension;
use Shopsys\FrameworkBundle\Twig\PriceExtension;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig_Environment;

class OrderMail implements MessageFactoryInterface
{
    /** @access protected */
    const MAIL_TEMPLATE_NAME_PREFIX = 'order_status_';
    public const VARIABLE_NUMBER = '{number}';
    public const VARIABLE_DATE = '{date}';
    public const VARIABLE_URL = '{url}';
    public const VARIABLE_TRANSPORT = '{transport}';
    public const VARIABLE_PAYMENT = '{payment}';
    public const VARIABLE_TOTAL_PRICE = '{total_price}';
    public const VARIABLE_BILLING_ADDRESS = '{billing_address}';
    public const VARIABLE_DELIVERY_ADDRESS = '{delivery_address}';
    public const VARIABLE_NOTE = '{note}';
    public const VARIABLE_PRODUCTS = '{products}';
    public const VARIABLE_ORDER_DETAIL_URL = '{order_detail_url}';
    public const VARIABLE_TRANSPORT_INSTRUCTIONS = '{transport_instructions}';
    public const VARIABLE_PAYMENT_INSTRUCTIONS = '{payment_instructions}';

    /**
     * @var \Shopsys\FrameworkBundle\Component\Setting\Setting
     */
    protected $setting;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Router\DomainRouterFactory
     */
    protected $domainRouterFactory;

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\Item\OrderItemPriceCalculation
     */
    protected $orderItemPriceCalculation;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    protected $domain;

    /**
     * @var \Shopsys\FrameworkBundle\Twig\PriceExtension
     */
    protected $priceExtension;

    /**
     * @var \Shopsys\FrameworkBundle\Twig\DateTimeFormatterExtension
     */
    protected $dateTimeFormatterExtension;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\OrderUrlGenerator
     */
    protected $orderUrlGenerator;

    /**
     * @param \Shopsys\FrameworkBundle\Component\Setting\Setting $setting
     * @param \Shopsys\FrameworkBundle\Component\Router\DomainRouterFactory $domainRouterFactory
     * @param \Twig_Environment $twig
     * @param \Shopsys\FrameworkBundle\Model\Order\Item\OrderItemPriceCalculation $orderItemPriceCalculation
     * @param \Shopsys\FrameworkBundle\Component\Domain\Domain $domain
     * @param \Shopsys\FrameworkBundle\Twig\PriceExtension $priceExtension
     * @param \Shopsys\FrameworkBundle\Twig\DateTimeFormatterExtension $dateTimeFormatterExtension
     * @param \Shopsys\FrameworkBundle\Model\Order\OrderUrlGenerator $orderUrlGenerator
     */
    public function __construct(
        Setting $setting,
        DomainRouterFactory $domainRouterFactory,
        Twig_Environment $twig,
        OrderItemPriceCalculation $orderItemPriceCalculation,
        Domain $domain,
        PriceExtension $priceExtension,
        DateTimeFormatterExtension $dateTimeFormatterExtension,
        OrderUrlGenerator $orderUrlGenerator
    ) {
        $this->setting = $setting;
        $this->domainRouterFactory = $domainRouterFactory;
        $this->twig = $twig;
        $this->orderItemPriceCalculation = $orderItemPriceCalculation;
        $this->domain = $domain;
        $this->priceExtension = $priceExtension;
        $this->dateTimeFormatterExtension = $dateTimeFormatterExtension;
        $this->orderUrlGenerator = $orderUrlGenerator;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Mail\MailTemplate $mailTemplate
     * @param \Shopsys\FrameworkBundle\Model\Order\Order $order
     * @return \Shopsys\FrameworkBundle\Model\Mail\MessageData
     */
    public function createMessage(MailTemplate $mailTemplate, $order)
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
    public static function getMailTemplateNameByStatus(OrderStatus $orderStatus)
    {
        return static::MAIL_TEMPLATE_NAME_PREFIX . $orderStatus->getId();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Mail\MailTemplate[] $mailTemplates
     * @param \Shopsys\FrameworkBundle\Model\Order\Status\OrderStatus $orderStatus
     * @return \Shopsys\FrameworkBundle\Model\Mail\MailTemplate|null
     */
    public static function findMailTemplateForOrderStatus(array $mailTemplates, OrderStatus $orderStatus)
    {
        foreach ($mailTemplates as $mailTemplate) {
            if ($mailTemplate->getName() === self::getMailTemplateNameByStatus($orderStatus)) {
                return $mailTemplate;
            }
        }

        return null;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Order $order
     * @return array
     */
    protected function getVariablesReplacementsForBody(Order $order)
    {
        $router = $this->domainRouterFactory->getRouter($order->getDomainId());
        $orderDomainConfig = $this->domain->getDomainConfigById($order->getDomainId());

        $transport = $order->getTransport();
        $payment = $order->getPayment();

        $transportInstructions = $transport->getInstructions($orderDomainConfig->getLocale());
        $paymentInstructions = $payment->getInstructions($orderDomainConfig->getLocale());

        return [
            self::VARIABLE_NUMBER => htmlspecialchars($order->getNumber(), ENT_QUOTES),
            self::VARIABLE_DATE => $this->getFormattedDateTime($order),
            self::VARIABLE_URL => $router->generate('front_homepage', [], UrlGeneratorInterface::ABSOLUTE_URL),
            self::VARIABLE_TRANSPORT => htmlspecialchars($order->getTransportName(), ENT_QUOTES),
            self::VARIABLE_PAYMENT => htmlspecialchars($order->getPaymentName(), ENT_QUOTES),
            self::VARIABLE_TOTAL_PRICE => $this->getFormattedPrice($order),
            self::VARIABLE_BILLING_ADDRESS => $this->getBillingAddressHtmlTable($order),
            self::VARIABLE_DELIVERY_ADDRESS => $this->getDeliveryAddressHtmlTable($order),
            self::VARIABLE_NOTE => htmlspecialchars($order->getNote(), ENT_QUOTES),
            self::VARIABLE_PRODUCTS => $this->getProductsHtmlTable($order),
            self::VARIABLE_ORDER_DETAIL_URL => $this->orderUrlGenerator->getOrderDetailUrl($order),
            self::VARIABLE_TRANSPORT_INSTRUCTIONS => $transportInstructions,
            self::VARIABLE_PAYMENT_INSTRUCTIONS => $paymentInstructions,
        ];
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Order $order
     * @return array
     */
    protected function getVariablesReplacementsForSubject(Order $order)
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
    protected function getFormattedPrice(Order $order)
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
    protected function getFormattedDateTime(Order $order)
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
    protected function getBillingAddressHtmlTable(Order $order)
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
    protected function getDeliveryAddressHtmlTable(Order $order)
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
    protected function getProductsHtmlTable(Order $order)
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
    protected function getDomainLocaleByOrder(Order $order)
    {
        return $this->domain->getDomainConfigById($order->getDomainId())->getLocale();
    }
}
