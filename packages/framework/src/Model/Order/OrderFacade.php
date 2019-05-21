<?php

namespace Shopsys\FrameworkBundle\Model\Order;

use Doctrine\ORM\EntityManagerInterface;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\Setting\Setting;
use Shopsys\FrameworkBundle\Form\Admin\QuickSearch\QuickSearchFormData;
use Shopsys\FrameworkBundle\Model\Administrator\Security\AdministratorFrontSecurityFacade;
use Shopsys\FrameworkBundle\Model\Cart\CartFacade;
use Shopsys\FrameworkBundle\Model\Customer\CurrentCustomer;
use Shopsys\FrameworkBundle\Model\Customer\CustomerFacade;
use Shopsys\FrameworkBundle\Model\Customer\User;
use Shopsys\FrameworkBundle\Model\Heureka\HeurekaFacade;
use Shopsys\FrameworkBundle\Model\Localization\Localization;
use Shopsys\FrameworkBundle\Model\Order\Item\OrderItemFactoryInterface;
use Shopsys\FrameworkBundle\Model\Order\Item\OrderItemPriceCalculation;
use Shopsys\FrameworkBundle\Model\Order\Item\OrderProductFacade;
use Shopsys\FrameworkBundle\Model\Order\Mail\OrderMailFacade;
use Shopsys\FrameworkBundle\Model\Order\Preview\OrderPreview;
use Shopsys\FrameworkBundle\Model\Order\Preview\OrderPreviewFactory;
use Shopsys\FrameworkBundle\Model\Order\PromoCode\CurrentPromoCodeFacade;
use Shopsys\FrameworkBundle\Model\Order\Status\OrderStatus;
use Shopsys\FrameworkBundle\Model\Order\Status\OrderStatusRepository;
use Shopsys\FrameworkBundle\Model\Payment\PaymentPriceCalculation;
use Shopsys\FrameworkBundle\Model\Transport\TransportPriceCalculation;
use Shopsys\FrameworkBundle\Twig\NumberFormatterExtension;

class OrderFacade
{
    public const VARIABLE_NUMBER = '{number}';
    public const VARIABLE_ORDER_DETAIL_URL = '{order_detail_url}';
    public const VARIABLE_PAYMENT_INSTRUCTIONS = '{payment_instructions}';
    public const VARIABLE_TRANSPORT_INSTRUCTIONS = '{transport_instructions}';

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $em;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\OrderNumberSequenceRepository
     */
    protected $orderNumberSequenceRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\OrderRepository
     */
    protected $orderRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\OrderUrlGenerator
     */
    protected $orderUrlGenerator;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\Status\OrderStatusRepository
     */
    protected $orderStatusRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\Mail\OrderMailFacade
     */
    protected $orderMailFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\OrderHashGeneratorRepository
     */
    protected $orderHashGeneratorRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Setting\Setting
     */
    protected $setting;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Localization\Localization
     */
    protected $localization;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Administrator\Security\AdministratorFrontSecurityFacade
     */
    protected $administratorFrontSecurityFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\PromoCode\CurrentPromoCodeFacade
     */
    protected $currentPromoCodeFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Cart\CartFacade
     */
    protected $cartFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Customer\CustomerFacade
     */
    protected $customerFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Customer\CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\Preview\OrderPreviewFactory
     */
    protected $orderPreviewFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\Item\OrderProductFacade
     */
    protected $orderProductFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Heureka\HeurekaFacade
     */
    protected $heurekaFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    protected $domain;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\OrderFactoryInterface
     */
    protected $orderFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\OrderPriceCalculation
     */
    protected $orderPriceCalculation;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\Item\OrderItemPriceCalculation
     */
    protected $orderItemPriceCalculation;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\FrontOrderDataMapper
     */
    protected $frontOrderDataMapper;

    /**
     * @var \Shopsys\FrameworkBundle\Twig\NumberFormatterExtension
     */
    protected $numberFormatterExtension;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Payment\PaymentPriceCalculation
     */
    protected $paymentPriceCalculation;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Transport\TransportPriceCalculation
     */
    protected $transportPriceCalculation;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\Item\OrderItemFactoryInterface
     */
    protected $orderItemFactory;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface $em
     * @param \Shopsys\FrameworkBundle\Model\Order\OrderNumberSequenceRepository $orderNumberSequenceRepository
     * @param \Shopsys\FrameworkBundle\Model\Order\OrderRepository $orderRepository
     * @param \Shopsys\FrameworkBundle\Model\Order\OrderUrlGenerator $orderUrlGenerator
     * @param \Shopsys\FrameworkBundle\Model\Order\Status\OrderStatusRepository $orderStatusRepository
     * @param \Shopsys\FrameworkBundle\Model\Order\Mail\OrderMailFacade $orderMailFacade
     * @param \Shopsys\FrameworkBundle\Model\Order\OrderHashGeneratorRepository $orderHashGeneratorRepository
     * @param \Shopsys\FrameworkBundle\Component\Setting\Setting $setting
     * @param \Shopsys\FrameworkBundle\Model\Localization\Localization $localization
     * @param \Shopsys\FrameworkBundle\Model\Administrator\Security\AdministratorFrontSecurityFacade $administratorFrontSecurityFacade
     * @param \Shopsys\FrameworkBundle\Model\Order\PromoCode\CurrentPromoCodeFacade $currentPromoCodeFacade
     * @param \Shopsys\FrameworkBundle\Model\Cart\CartFacade $cartFacade
     * @param \Shopsys\FrameworkBundle\Model\Customer\CustomerFacade $customerFacade
     * @param \Shopsys\FrameworkBundle\Model\Customer\CurrentCustomer $currentCustomer
     * @param \Shopsys\FrameworkBundle\Model\Order\Preview\OrderPreviewFactory $orderPreviewFactory
     * @param \Shopsys\FrameworkBundle\Model\Order\Item\OrderProductFacade $orderProductFacade
     * @param \Shopsys\FrameworkBundle\Model\Heureka\HeurekaFacade $heurekaFacade
     * @param \Shopsys\FrameworkBundle\Component\Domain\Domain $domain
     * @param \Shopsys\FrameworkBundle\Model\Order\OrderFactoryInterface $orderFactory
     * @param \Shopsys\FrameworkBundle\Model\Order\OrderPriceCalculation $orderPriceCalculation
     * @param \Shopsys\FrameworkBundle\Model\Order\Item\OrderItemPriceCalculation $orderItemPriceCalculation
     * @param \Shopsys\FrameworkBundle\Model\Order\FrontOrderDataMapper $frontOrderDataMapper
     * @param \Shopsys\FrameworkBundle\Twig\NumberFormatterExtension $numberFormatterExtension
     * @param \Shopsys\FrameworkBundle\Model\Payment\PaymentPriceCalculation $paymentPriceCalculation
     * @param \Shopsys\FrameworkBundle\Model\Transport\TransportPriceCalculation $transportPriceCalculation
     * @param \Shopsys\FrameworkBundle\Model\Order\Item\OrderItemFactoryInterface $orderItemFactory
     */
    public function __construct(
        EntityManagerInterface $em,
        OrderNumberSequenceRepository $orderNumberSequenceRepository,
        OrderRepository $orderRepository,
        OrderUrlGenerator $orderUrlGenerator,
        OrderStatusRepository $orderStatusRepository,
        OrderMailFacade $orderMailFacade,
        OrderHashGeneratorRepository $orderHashGeneratorRepository,
        Setting $setting,
        Localization $localization,
        AdministratorFrontSecurityFacade $administratorFrontSecurityFacade,
        CurrentPromoCodeFacade $currentPromoCodeFacade,
        CartFacade $cartFacade,
        CustomerFacade $customerFacade,
        CurrentCustomer $currentCustomer,
        OrderPreviewFactory $orderPreviewFactory,
        OrderProductFacade $orderProductFacade,
        HeurekaFacade $heurekaFacade,
        Domain $domain,
        OrderFactoryInterface $orderFactory,
        OrderPriceCalculation $orderPriceCalculation,
        OrderItemPriceCalculation $orderItemPriceCalculation,
        FrontOrderDataMapper $frontOrderDataMapper,
        NumberFormatterExtension $numberFormatterExtension,
        PaymentPriceCalculation $paymentPriceCalculation,
        TransportPriceCalculation $transportPriceCalculation,
        OrderItemFactoryInterface $orderItemFactory
    ) {
        $this->em = $em;
        $this->orderNumberSequenceRepository = $orderNumberSequenceRepository;
        $this->orderRepository = $orderRepository;
        $this->orderStatusRepository = $orderStatusRepository;
        $this->orderMailFacade = $orderMailFacade;
        $this->orderHashGeneratorRepository = $orderHashGeneratorRepository;
        $this->setting = $setting;
        $this->localization = $localization;
        $this->administratorFrontSecurityFacade = $administratorFrontSecurityFacade;
        $this->currentPromoCodeFacade = $currentPromoCodeFacade;
        $this->cartFacade = $cartFacade;
        $this->customerFacade = $customerFacade;
        $this->currentCustomer = $currentCustomer;
        $this->orderPreviewFactory = $orderPreviewFactory;
        $this->orderProductFacade = $orderProductFacade;
        $this->heurekaFacade = $heurekaFacade;
        $this->domain = $domain;
        $this->orderFactory = $orderFactory;
        $this->orderPriceCalculation = $orderPriceCalculation;
        $this->orderUrlGenerator = $orderUrlGenerator;
        $this->orderItemPriceCalculation = $orderItemPriceCalculation;
        $this->frontOrderDataMapper = $frontOrderDataMapper;
        $this->numberFormatterExtension = $numberFormatterExtension;
        $this->paymentPriceCalculation = $paymentPriceCalculation;
        $this->transportPriceCalculation = $transportPriceCalculation;
        $this->orderItemFactory = $orderItemFactory;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\OrderData $orderData
     * @param \Shopsys\FrameworkBundle\Model\Order\Preview\OrderPreview $orderPreview
     * @param \Shopsys\FrameworkBundle\Model\Customer\User|null $user
     * @return \Shopsys\FrameworkBundle\Model\Order\Order
     */
    public function createOrder(OrderData $orderData, OrderPreview $orderPreview, ?User $user = null)
    {
        $orderNumber = $this->orderNumberSequenceRepository->getNextNumber();
        $orderUrlHash = $this->orderHashGeneratorRepository->getUniqueHash();
        $toFlush = [];

        $this->setOrderDataAdministrator($orderData);

        $order = $this->orderFactory->create(
            $orderData,
            $orderNumber,
            $orderUrlHash,
            $user
        );
        $toFlush[] = $order;

        $this->fillOrderItems($order, $orderPreview);

        foreach ($order->getItems() as $orderItem) {
            $this->em->persist($orderItem);
            $toFlush[] = $orderItem;
        }

        $order->calculateTotalPrice($this->orderPriceCalculation);
        $this->em->persist($order);
        $this->em->flush($toFlush);

        return $order;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\OrderData $orderData
     * @return \Shopsys\FrameworkBundle\Model\Order\Order
     */
    public function createOrderFromFront(OrderData $orderData)
    {
        $orderData->status = $this->orderStatusRepository->getDefault();
        $orderPreview = $this->orderPreviewFactory->createForCurrentUser($orderData->transport, $orderData->payment);
        $user = $this->currentCustomer->findCurrentUser();

        $order = $this->createOrder($orderData, $orderPreview, $user);
        $this->orderProductFacade->subtractOrderProductsFromStock($order->getProductItems());

        $this->cartFacade->deleteCartOfCurrentCustomer();
        $this->currentPromoCodeFacade->removeEnteredPromoCode();
        if ($user instanceof User) {
            $this->customerFacade->amendCustomerDataFromOrder($user, $order);
        }

        return $order;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Order $order
     * @param bool $disallowHeurekaVerifiedByCustomers
     */
    public function sendHeurekaOrderInfo(Order $order, $disallowHeurekaVerifiedByCustomers)
    {
        $domainConfig = $this->domain->getDomainConfigById($order->getDomainId());
        $locale = $domainConfig->getLocale();

        if ($this->heurekaFacade->isHeurekaShopCertificationActivated($order->getDomainId()) &&
            $this->heurekaFacade->isDomainLocaleSupported($locale) &&
            !$disallowHeurekaVerifiedByCustomers
        ) {
            $this->heurekaFacade->sendOrderInfo($order);
        }
    }

    /**
     * @param int $orderId
     * @param \Shopsys\FrameworkBundle\Model\Order\OrderData $orderData
     * @return \Shopsys\FrameworkBundle\Model\Order\Order
     */
    public function edit($orderId, OrderData $orderData)
    {
        $order = $this->orderRepository->getById($orderId);
        $originalOrderStatus = $order->getStatus();
        $orderEditResult = $order->edit(
            $orderData,
            $this->orderItemPriceCalculation,
            $this->orderItemFactory,
            $this->orderPriceCalculation
        );

        $this->em->flush();
        if ($orderEditResult->isStatusChanged()) {
            $mailTemplate = $this->orderMailFacade
                ->getMailTemplateByStatusAndDomainId($order->getStatus(), $order->getDomainId());
            if ($mailTemplate->isSendMail()) {
                $this->orderMailFacade->sendEmail($order);
            }
            if ($originalOrderStatus->getType() === OrderStatus::TYPE_CANCELED) {
                $this->orderProductFacade->subtractOrderProductsFromStock($order->getProductItems());
            }
            if ($orderData->status->getType() === OrderStatus::TYPE_CANCELED) {
                $this->orderProductFacade->addOrderProductsToStock($order->getProductItems());
            }
        }

        return $order;
    }

    /**
     * @param int $orderId
     * @return string
     */
    public function getOrderSentPageContent($orderId)
    {
        $order = $this->getById($orderId);
        $orderDetailUrl = $this->orderUrlGenerator->getOrderDetailUrl($order);
        $orderSentPageContent = $this->setting->getForDomain(Setting::ORDER_SENT_PAGE_CONTENT, $order->getDomainId());

        $variables = [
            self::VARIABLE_TRANSPORT_INSTRUCTIONS => $order->getTransport()->getInstructions(),
            self::VARIABLE_PAYMENT_INSTRUCTIONS => $order->getPayment()->getInstructions(),
            self::VARIABLE_ORDER_DETAIL_URL => $orderDetailUrl,
            self::VARIABLE_NUMBER => $order->getNumber(),
        ];

        return strtr($orderSentPageContent, $variables);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\FrontOrderData $orderData
     * @param \Shopsys\FrameworkBundle\Model\Customer\User $user
     */
    public function prefillFrontOrderData(FrontOrderData $orderData, User $user)
    {
        $order = $this->orderRepository->findLastByUserId($user->getId());
        $this->frontOrderDataMapper->prefillFrontFormData($orderData, $user, $order);
    }

    /**
     * @param int $orderId
     */
    public function deleteById($orderId)
    {
        $order = $this->orderRepository->getById($orderId);
        if ($order->getStatus()->getType() !== OrderStatus::TYPE_CANCELED) {
            $this->orderProductFacade->addOrderProductsToStock($order->getProductItems());
        }
        $order->markAsDeleted();
        $this->em->flush();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Customer\User $user
     * @return \Shopsys\FrameworkBundle\Model\Order\Order[]
     */
    public function getCustomerOrderList(User $user)
    {
        return $this->orderRepository->getCustomerOrderList($user);
    }

    /**
     * @param string $email
     * @param int $domainId
     * @return \Shopsys\FrameworkBundle\Model\Order\Order[]
     */
    public function getOrderListForEmailByDomainId($email, $domainId)
    {
        return $this->orderRepository->getOrderListForEmailByDomainId($email, $domainId);
    }

    /**
     * @param int $orderId
     * @return \Shopsys\FrameworkBundle\Model\Order\Order
     */
    public function getById($orderId)
    {
        return $this->orderRepository->getById($orderId);
    }

    /**
     * @param string $urlHash
     * @param int $domainId
     * @return \Shopsys\FrameworkBundle\Model\Order\Order
     */
    public function getByUrlHashAndDomain($urlHash, $domainId)
    {
        return $this->orderRepository->getByUrlHashAndDomain($urlHash, $domainId);
    }

    /**
     * @param string $orderNumber
     * @param \Shopsys\FrameworkBundle\Model\Customer\User $user
     * @return \Shopsys\FrameworkBundle\Model\Order\Order
     */
    public function getByOrderNumberAndUser($orderNumber, User $user)
    {
        return $this->orderRepository->getByOrderNumberAndUser($orderNumber, $user);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Form\Admin\QuickSearch\QuickSearchFormData $quickSearchData
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getOrderListQueryBuilderByQuickSearchData(QuickSearchFormData $quickSearchData)
    {
        return $this->orderRepository->getOrderListQueryBuilderByQuickSearchData(
            $this->localization->getAdminLocale(),
            $quickSearchData
        );
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\OrderData $orderData
     */
    protected function setOrderDataAdministrator(OrderData $orderData)
    {
        if ($this->administratorFrontSecurityFacade->isAdministratorLoggedAsCustomer()) {
            try {
                $currentAdmin = $this->administratorFrontSecurityFacade->getCurrentAdministrator();
                $orderData->createdAsAdministrator = $currentAdmin;
                $orderData->createdAsAdministratorName = $currentAdmin->getRealName();
            } catch (\Shopsys\FrameworkBundle\Model\Administrator\Security\Exception\AdministratorIsNotLoggedException $ex) {
            }
        }
    }

    /**
     * @param string $email
     * @param int $domainId
     */
    public function getOrdersCountByEmailAndDomainId($email, $domainId)
    {
        return $this->orderRepository->getOrdersCountByEmailAndDomainId($email, $domainId);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Order $order
     * @param \Shopsys\FrameworkBundle\Model\Order\Preview\OrderPreview $orderPreview
     */
    protected function fillOrderItems(Order $order, OrderPreview $orderPreview)
    {
        $locale = $this->domain->getDomainConfigById($order->getDomainId())->getLocale();

        $order->fillOrderProducts($orderPreview, $this->orderItemFactory, $this->numberFormatterExtension, $locale);
        $order->fillOrderPayment($this->paymentPriceCalculation, $this->orderItemFactory, $orderPreview->getProductsPrice(), $locale);
        $order->fillOrderTransport($this->transportPriceCalculation, $this->orderItemFactory, $orderPreview->getProductsPrice(), $locale);
        $order->fillOrderRounding($this->orderItemFactory, $orderPreview->getRoundingPrice(), $locale);
    }
}
