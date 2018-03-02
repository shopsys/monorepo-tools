<?php

namespace Shopsys\FrameworkBundle\Model\Order;

use Doctrine\ORM\EntityManager;
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
use Shopsys\FrameworkBundle\Model\Order\Item\OrderProductFacade;
use Shopsys\FrameworkBundle\Model\Order\Mail\OrderMailFacade;
use Shopsys\FrameworkBundle\Model\Order\Preview\OrderPreview;
use Shopsys\FrameworkBundle\Model\Order\Preview\OrderPreviewFactory;
use Shopsys\FrameworkBundle\Model\Order\PromoCode\CurrentPromoCodeFacade;
use Shopsys\FrameworkBundle\Model\Order\Status\OrderStatus;
use Shopsys\FrameworkBundle\Model\Order\Status\OrderStatusRepository;

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
     * @var \Shopsys\FrameworkBundle\Model\Order\OrderNumberSequenceRepository
     */
    private $orderNumberSequenceRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\OrderRepository
     */
    private $orderRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\OrderService
     */
    private $orderService;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\OrderCreationService
     */
    private $orderCreationService;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\Status\OrderStatusRepository
     */
    private $orderStatusRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\Mail\OrderMailFacade
     */
    private $orderMailFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\OrderHashGeneratorRepository
     */
    private $orderHashGeneratorRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Setting\Setting
     */
    private $setting;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Localization\Localization
     */
    private $localization;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Administrator\Security\AdministratorFrontSecurityFacade
     */
    private $administratorFrontSecurityFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\PromoCode\CurrentPromoCodeFacade
     */
    private $currentPromoCodeFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Cart\CartFacade
     */
    private $cartFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Customer\CustomerFacade
     */
    private $customerFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Customer\CurrentCustomer
     */
    private $currentCustomer;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\Preview\OrderPreviewFactory
     */
    private $orderPreviewFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\Item\OrderProductFacade
     */
    private $orderProductFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Heureka\HeurekaFacade
     */
    private $heurekaFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    private $domain;

    public function __construct(
        EntityManager $em,
        OrderNumberSequenceRepository $orderNumberSequenceRepository,
        OrderRepository $orderRepository,
        OrderService $orderService,
        OrderCreationService $orderCreationService,
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
        Domain $domain
    ) {
        $this->em = $em;
        $this->orderNumberSequenceRepository = $orderNumberSequenceRepository;
        $this->orderRepository = $orderRepository;
        $this->orderService = $orderService;
        $this->orderCreationService = $orderCreationService;
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
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\OrderData $orderData
     * @param \Shopsys\FrameworkBundle\Model\Order\Preview\OrderPreview $orderPreview
     * @param \Shopsys\FrameworkBundle\Model\Customer\User|null $user
     * @return \Shopsys\FrameworkBundle\Model\Order\Order
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
     * @param \Shopsys\FrameworkBundle\Model\Order\OrderData $orderData
     * @return \Shopsys\FrameworkBundle\Model\Order\Order
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
        if ($this->heurekaFacade->isHeurekaShopCertificationActivated($orderData->domainId) &&
            $this->heurekaFacade->isDomainLocaleSupported($locale)
        ) {
            $this->heurekaFacade->sendOrderInfo($order);
        }

        return $order;
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
     * @param int $orderId
     * @return string
     */
    public function getOrderSentPageContent($orderId)
    {
        $order = $this->getById($orderId);
        $orderDetailUrl = $this->orderService->getOrderDetailUrl($order);
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
     * @param \Shopsys\FrameworkBundle\Model\Customer\User $user
     * @return \Shopsys\FrameworkBundle\Model\Order\Order[]
     */
    public function getCustomerOrderList(User $user)
    {
        return $this->orderRepository->getCustomerOrderList($user);
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
    private function setOrderDataAdministrator(OrderData $orderData)
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
}
