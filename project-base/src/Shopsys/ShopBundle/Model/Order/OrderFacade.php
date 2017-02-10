<?php

namespace Shopsys\ShopBundle\Model\Order;

use Doctrine\ORM\EntityManager;
use Exception;
use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Component\Router\DomainRouterFactory;
use Shopsys\ShopBundle\Component\Setting\Setting;
use Shopsys\ShopBundle\Form\Admin\QuickSearch\QuickSearchFormData;
use Shopsys\ShopBundle\Model\Administrator\Security\AdministratorFrontSecurityFacade;
use Shopsys\ShopBundle\Model\Cart\CartFacade;
use Shopsys\ShopBundle\Model\Customer\CurrentCustomer;
use Shopsys\ShopBundle\Model\Customer\CustomerFacade;
use Shopsys\ShopBundle\Model\Customer\User;
use Shopsys\ShopBundle\Model\Customer\UserRepository;
use Shopsys\ShopBundle\Model\Heureka\HeurekaFacade;
use Shopsys\ShopBundle\Model\Localization\Localization;
use Shopsys\ShopBundle\Model\Order\Item\OrderProductFacade;
use Shopsys\ShopBundle\Model\Order\Mail\OrderMailFacade;
use Shopsys\ShopBundle\Model\Order\Order;
use Shopsys\ShopBundle\Model\Order\OrderCreationService;
use Shopsys\ShopBundle\Model\Order\OrderData;
use Shopsys\ShopBundle\Model\Order\OrderHashGeneratorRepository;
use Shopsys\ShopBundle\Model\Order\OrderNumberSequenceRepository;
use Shopsys\ShopBundle\Model\Order\OrderService;
use Shopsys\ShopBundle\Model\Order\Preview\OrderPreview;
use Shopsys\ShopBundle\Model\Order\Preview\OrderPreviewFactory;
use Shopsys\ShopBundle\Model\Order\PromoCode\CurrentPromoCodeFacade;
use Shopsys\ShopBundle\Model\Order\Status\OrderStatus;
use Shopsys\ShopBundle\Model\Order\Status\OrderStatusRepository;

class OrderFacade
{
    const VARIABLE_NUMBER = '{number}';
    const VARIABLE_ORDER_DETAIL_URL = '{order_detail_url}';
    const VARIABLE_PAYMENT_INSTRUCTIONS = '{payment_instructions}';
    const VARIABLE_TRANSPORT_INSTRUCTIONS = '{transport_instructions}';

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Shopsys\ShopBundle\Model\Order\OrderNumberSequenceRepository
     */
    private $orderNumberSequenceRepository;

    /**
     * @var \Shopsys\ShopBundle\Model\Order\OrderRepository
     */
    private $orderRepository;

    /**
     * @var \Shopsys\ShopBundle\Model\Order\OrderService
     */
    private $orderService;

    /**
     * @var \Shopsys\ShopBundle\Model\Order\OrderCreationService
     */
    private $orderCreationService;

    /**
     * @var \Shopsys\ShopBundle\Model\Customer\UserRepository
     */
    private $userRepository;

    /**
     * @var \Shopsys\ShopBundle\Model\Order\Status\OrderStatusRepository
     */
    private $orderStatusRepository;

    /**
     * @var \Shopsys\ShopBundle\Model\Order\Mail\OrderMailFacade
     */
    private $orderMailFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\Order\OrderHashGeneratorRepository
     */
    private $orderHashGeneratorRepository;

    /**
     * @var \Shopsys\ShopBundle\Component\Setting\Setting
     */
    private $setting;

    /**
     * @var \Shopsys\ShopBundle\Model\Localization\Localization
     */
    private $localization;

    /**
     * @var \Shopsys\ShopBundle\Model\Administrator\Security\AdministratorFrontSecurityFacade
     */
    private $administratorFrontSecurityFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\Order\PromoCode\CurrentPromoCodeFacade
     */
    private $currentPromoCodeFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\Cart\CartFacade
     */
    private $cartFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\Customer\CustomerFacade
     */
    private $customerFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\Customer\CurrentCustomer
     */
    private $currentCustomer;

    /**
     * @var \Shopsys\ShopBundle\Model\Order\Preview\OrderPreviewFactory
     */
    private $orderPreviewFactory;

    /**
     * @var \Shopsys\ShopBundle\Model\Order\Item\OrderProductFacade
     */
    private $orderProductFacade;

    /**
     * @var \Shopsys\ShopBundle\Component\Router\DomainRouterFactory
     */
    private $domainRouterFactory;

    /**
     * @var \Shopsys\ShopBundle\Model\Heureka\HeurekaFacade
     */
    private $heurekaFacade;

    /**
     * @var \Shopsys\ShopBundle\Component\Domain\Domain
     */
    private $domain;

    public function __construct(
        EntityManager $em,
        OrderNumberSequenceRepository $orderNumberSequenceRepository,
        OrderRepository $orderRepository,
        OrderService $orderService,
        OrderCreationService $orderCreationService,
        UserRepository $userRepository,
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
        DomainRouterFactory $domainRouterFactory,
        HeurekaFacade $heurekaFacade,
        Domain $domain
    ) {
        $this->em = $em;
        $this->orderNumberSequenceRepository = $orderNumberSequenceRepository;
        $this->orderRepository = $orderRepository;
        $this->orderService = $orderService;
        $this->orderCreationService = $orderCreationService;
        $this->userRepository = $userRepository;
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
        $this->domainRouterFactory = $domainRouterFactory;
        $this->heurekaFacade = $heurekaFacade;
        $this->domain = $domain;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Order\OrderData $orderData
     * @param \Shopsys\ShopBundle\Model\Order\Preview\OrderPreview $orderPreview
     * @param \Shopsys\ShopBundle\Model\Customer\User|null $user
     * @return \Shopsys\ShopBundle\Model\Order\Order
     */
    public function createOrder(OrderData $orderData, OrderPreview $orderPreview, User $user = null)
    {
        $orderNumber = $this->orderNumberSequenceRepository->getNextNumber();
        $orderUrlHash = $this->orderHashGeneratorRepository->getUniqueHash();
        $toFlush = [];

        $this->setOrderDataAdministrator($orderData);

        $order = new Order(
            $orderData,
            $orderNumber,
            $orderUrlHash,
            $user
        );
        $toFlush[] = $order;

        $this->orderCreationService->fillOrderItems($order, $orderPreview);

        foreach ($order->getItems() as $orderItem) {
            $this->em->persist($orderItem);
            $toFlush[] = $orderItem;
        }

        $this->orderService->calculateTotalPrice($order);
        $this->em->persist($order);
        $this->em->flush($toFlush);

        return $order;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Order\OrderData $orderData
     * @return \Shopsys\ShopBundle\Model\Order\Order
     */
    public function createOrderFromFront(OrderData $orderData)
    {
        $domainConfig = $this->domain->getDomainConfigById($orderData->domainId);
        $locale = $domainConfig->getLocale();

        $orderData->status = $this->orderStatusRepository->getDefault();
        $orderPreview = $this->orderPreviewFactory->createForCurrentUser($orderData->transport, $orderData->payment);
        $user = $this->currentCustomer->findCurrentUser();

        $order = $this->createOrder($orderData, $orderPreview, $user);
        $this->orderProductFacade->subtractOrderProductsFromStock($order->getProductItems());

        $this->cartFacade->cleanCart();
        $this->currentPromoCodeFacade->removeEnteredPromoCode();
        if ($user instanceof User) {
            $this->customerFacade->amendCustomerDataFromOrder($user, $order);
        }
        if (
            $this->heurekaFacade->isHeurekaShopCertificationActivated($orderData->domainId) &&
            $this->heurekaFacade->isDomainLocaleSupported($locale)
        ) {
            $this->heurekaFacade->sendOrderInfo($order);
        }

        return $order;
    }

    /**
     * @param int $orderId
     * @param \Shopsys\ShopBundle\Model\Order\OrderData $orderData
     * @return \Shopsys\ShopBundle\Model\Order\Order
     */
    public function edit($orderId, OrderData $orderData)
    {
        $order = $this->orderRepository->getById($orderId);
        $originalOrderStatus = $order->getStatus();
        $orderEditResult = $this->orderService->editOrder($order, $orderData);

        foreach ($orderEditResult->getOrderItemsToCreate() as $orderItem) {
            $this->em->persist($orderItem);
        }
        foreach ($orderEditResult->getOrderItemsToDelete() as $orderItem) {
            $this->em->remove($orderItem);
        }

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
     * @param string $confirmTextTemplate
     * @param int $orderId
     * @return string
     */
    public function getOrderConfirmText($orderId)
    {
        $order = $this->getById($orderId);
        $orderDetailUrl = $this->orderService->getOrderDetailUrl($order);
        $confirmTextTemplate = $this->setting->getForDomain(Setting::ORDER_SUBMITTED_SETTING_NAME, $order->getDomainId());

        $variables = [
            self::VARIABLE_TRANSPORT_INSTRUCTIONS => $order->getTransport()->getInstructions(),
            self::VARIABLE_PAYMENT_INSTRUCTIONS => $order->getPayment()->getInstructions(),
            self::VARIABLE_ORDER_DETAIL_URL => $orderDetailUrl,
            self::VARIABLE_NUMBER => $order->getNumber(),
        ];

        return strtr($confirmTextTemplate, $variables);
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Order\FrontOrderData $orderData
     * @param \Shopsys\ShopBundle\Model\Customer\User $user
     */
    public function prefillFrontOrderData(FrontOrderData $orderData, User $user)
    {
        $order = $this->orderRepository->findLastByUserId($user->getId());
        $this->orderCreationService->prefillFrontFormData($orderData, $user, $order);
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
     * @param \Shopsys\ShopBundle\Model\Customer\User $user
     * @return \Shopsys\ShopBundle\Model\Order\Order[]
     */
    public function getCustomerOrderList(User $user)
    {
        return $this->orderRepository->getCustomerOrderList($user);
    }

    /**
     * @param int $orderId
     * @return \Shopsys\ShopBundle\Model\Order\Order
     */
    public function getById($orderId)
    {
        return $this->orderRepository->getById($orderId);
    }

    /**
     * @param string $urlHash
     * @param int $domainId
     * @return \Shopsys\ShopBundle\Model\Order\Order
     */
    public function getByUrlHashAndDomain($urlHash, $domainId)
    {
        return $this->orderRepository->getByUrlHashAndDomain($urlHash, $domainId);
    }

    /**
     * @param string $orderNumber
     * @param \Shopsys\ShopBundle\Model\Customer\User $user
     * @return \Shopsys\ShopBundle\Model\Order\Order
     */
    public function getByOrderNumberAndUser($orderNumber, User $user)
    {
        return $this->orderRepository->getByOrderNumberAndUser($orderNumber, $user);
    }

    /**
     * @param \Shopsys\ShopBundle\Form\Admin\QuickSearch\QuickSearchFormData $quickSearchData
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getOrderListQueryBuilderByQuickSearchData(QuickSearchFormData $quickSearchData)
    {
        return $this->orderRepository->getOrderListQueryBuilderByQuickSearchData(
            $this->localization->getDefaultLocale(),
            $quickSearchData
        );
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Order\OrderData $orderData
     */
    private function setOrderDataAdministrator(OrderData $orderData)
    {
        if ($this->administratorFrontSecurityFacade->isAdministratorLoggedAsCustomer()) {
            try {
                $currentAdmin = $this->administratorFrontSecurityFacade->getCurrentAdministrator();
                $orderData->createdAsAdministrator = $currentAdmin;
                $orderData->createdAsAdministratorName = $currentAdmin->getRealName();
            } catch (\Shopsys\ShopBundle\Model\Administrator\Security\Exception\AdministratorIsNotLoggedException $ex) {
            }
        }
    }
}
