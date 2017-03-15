<?php

namespace Shopsys\ShopBundle\Model\Order;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Join;
use Shopsys\ShopBundle\Component\String\DatabaseSearching;
use Shopsys\ShopBundle\Form\Admin\QuickSearch\QuickSearchFormData;
use Shopsys\ShopBundle\Model\Customer\User;
use Shopsys\ShopBundle\Model\Order\Listing\OrderListAdminRepository;
use Shopsys\ShopBundle\Model\Order\Order;
use Shopsys\ShopBundle\Model\Order\Status\OrderStatus;
use Shopsys\ShopBundle\Model\Pricing\Currency\Currency;

class OrderRepository
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Shopsys\ShopBundle\Model\Order\Listing\OrderListAdminRepository
     */
    private $orderListAdminRepository;

    public function __construct(
        EntityManager $em,
        OrderListAdminRepository $orderListAdminRepository
    ) {
        $this->em = $em;
        $this->orderListAdminRepository = $orderListAdminRepository;
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    private function getOrderRepository()
    {
        return $this->em->getRepository(Order::class);
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function createOrderQueryBuilder()
    {
        return $this->em->createQueryBuilder()
            ->select('o')
            ->from(Order::class, 'o')
            ->where('o.deleted = FALSE');
    }

    /**
     * @param int $userId
     * @return \Shopsys\ShopBundle\Model\Order\Order[]
     */
    public function getOrdersByUserId($userId)
    {
        return $this->createOrderQueryBuilder()
            ->andWhere('o.customer = :customer')->setParameter(':customer', $userId)
            ->getQuery()->getResult();
    }

    /**
     * @param int $userId
     * @return \Shopsys\ShopBundle\Model\Order\Order|null
     */
    public function findLastByUserId($userId)
    {
        return $this->createOrderQueryBuilder()
            ->andWhere('o.customer = :customer')->setParameter(':customer', $userId)
            ->orderBy('o.createdAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()->getOneOrNullResult();
    }

    /**
     * @param int $id
     * @return \Shopsys\ShopBundle\Model\Order\Order|null
     */
    public function findById($id)
    {
        return $this->createOrderQueryBuilder()
            ->andWhere('o.id = :orderId')->setParameter(':orderId', $id)
            ->setMaxResults(1)
            ->getQuery()->getOneOrNullResult();
    }

    /**
     * @param int $id
     * @return \Shopsys\ShopBundle\Model\Order\Order
     */
    public function getById($id)
    {
        $order = $this->findById($id);

        if ($order === null) {
            throw new \Shopsys\ShopBundle\Model\Order\Exception\OrderNotFoundException('Order with ID ' . $id . ' not found.');
        }

        return $order;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Order\Status\OrderStatus $orderStatus
     * @return bool
     */
    public function isOrderStatusUsed(OrderStatus $orderStatus)
    {
        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder
            ->select('o.id')
            ->from(Order::class, 'o')
            ->setMaxResults(1)
            ->where('o.status = :status')
            ->setParameter('status', $orderStatus->getId());

        return $queryBuilder->getQuery()->getOneOrNullResult(AbstractQuery::HYDRATE_SCALAR) !== null;
    }

    /**
     * @param string $locale
     * @param \Shopsys\ShopBundle\Form\Admin\QuickSearch\QuickSearchFormData $quickSearchData
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getOrderListQueryBuilderByQuickSearchData(
        $locale,
        QuickSearchFormData $quickSearchData
    ) {
        $queryBuilder = $this->orderListAdminRepository->getOrderListQueryBuilder($locale);

        if ($quickSearchData->text !== null && $quickSearchData->text !== '') {
            $queryBuilder
                ->leftJoin(User::class, 'u', Join::WITH, 'o.customer = u.id')
                ->andWhere('
                    (
                        o.number LIKE :text
                        OR
                        NORMALIZE(o.email) LIKE NORMALIZE(:text)
                        OR
                        NORMALIZE(o.lastName) LIKE NORMALIZE(:text)
                        OR
                        NORMALIZE(o.companyName) LIKE NORMALIZE(:text)
                        OR
                        NORMALIZE(u.email) LIKE NORMALIZE(:text)
                    )');
            $querySearchText = '%' . DatabaseSearching::getLikeSearchString($quickSearchData->text) . '%';
            $queryBuilder->setParameter('text', $querySearchText);
        }

        return $queryBuilder;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Customer\User
     * @return \Shopsys\ShopBundle\Model\Order\Order[]
     */
    public function getCustomerOrderList(User $user)
    {
        return $this->createOrderQueryBuilder()
            ->select('o, oi, os, ost, c')
            ->join('o.items', 'oi')
            ->join('o.status', 'os')
            ->join('os.translations', 'ost')
            ->join('o.currency', 'c')
            ->andWhere('o.customer = :customer')
            ->orderBy('o.createdAt', 'DESC')
            ->setParameter('customer', $user)
            ->getQuery()->execute();
    }

    /**
     * @param string $urlHash
     * @param int $domainId
     * @return \Shopsys\ShopBundle\Model\Order\Order
     */
    public function getByUrlHashAndDomain($urlHash, $domainId)
    {
        $order = $this->createOrderQueryBuilder()
            ->andWhere('o.urlHash = :urlHash')->setParameter(':urlHash', $urlHash)
            ->andWhere('o.domainId = :domainId')->setParameter(':domainId', $domainId)
            ->setMaxResults(1)
            ->getQuery()->getOneOrNullResult();

        if ($order === null) {
            throw new \Shopsys\ShopBundle\Model\Order\Exception\OrderNotFoundException();
        }

        return $order;
    }

    /**
     * @param string $orderNumber
     * @param \Shopsys\ShopBundle\Model\Customer\User $user
     * @return \Shopsys\ShopBundle\Model\Order\Order
     */
    public function getByOrderNumberAndUser($orderNumber, User $user)
    {
        $order = $this->createOrderQueryBuilder()
            ->andWhere('o.number = :number')->setParameter(':number', $orderNumber)
            ->andWhere('o.customer = :customer')->setParameter(':customer', $user)
            ->setMaxResults(1)
            ->getQuery()->getOneOrNullResult();

        if ($order === null) {
            $message = 'Order with number "' . $orderNumber . '" and userId "' . $user->getId() . '" not found.';
            throw new \Shopsys\ShopBundle\Model\Order\Exception\OrderNotFoundException($message);
        }

        return $order;
    }

    /**
     * @param string $urlHash
     * @return \Shopsys\ShopBundle\Model\Order\Order|null
     */
    public function findByUrlHashIncludingDeletedOrders($urlHash)
    {
        return $this->getOrderRepository()->findOneBy(['urlHash' => $urlHash]);
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Pricing\Currency\Currency[]
     */
    public function getCurrenciesUsedInOrders()
    {
        return $this->em->createQueryBuilder()
            ->select('c')
            ->from(Currency::class, 'c')
            ->join(Order::class, 'o', Join::WITH, 'o.currency = c.id')
            ->groupBy('c')
            ->getQuery()->execute();
    }
}
