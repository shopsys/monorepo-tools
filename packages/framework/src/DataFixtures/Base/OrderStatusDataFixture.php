<?php

namespace Shopsys\FrameworkBundle\DataFixtures\Base;

use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\FrameworkBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\FrameworkBundle\Model\Order\Status\OrderStatus;
use Shopsys\FrameworkBundle\Model\Order\Status\OrderStatusData;
use Shopsys\FrameworkBundle\Model\Order\Status\OrderStatusDataFactoryInterface;
use Shopsys\FrameworkBundle\Model\Order\Status\OrderStatusFactoryInterface;

class OrderStatusDataFixture extends AbstractReferenceFixture
{
    const ORDER_STATUS_NEW = 'order_status_new';
    const ORDER_STATUS_IN_PROGRESS = 'order_status_in_progress';
    const ORDER_STATUS_DONE = 'order_status_done';
    const ORDER_STATUS_CANCELED = 'order_status_canceled';

    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\Status\OrderStatusFactoryInterface
     */
    protected $orderStatusFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\Status\OrderStatusDataFactoryInterface
     */
    private $orderStatusDataFactory;

    public function __construct(
        OrderStatusFactoryInterface $orderStatusFactory,
        OrderStatusDataFactoryInterface $orderStatusDataFactory
    ) {
        $this->orderStatusFactory = $orderStatusFactory;
        $this->orderStatusDataFactory = $orderStatusDataFactory;
    }

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $orderStatusData = $this->orderStatusDataFactory->create();
        $orderStatusData->name = ['cs' => 'Nová', 'en' => 'New'];
        $this->createOrderStatus($manager, $orderStatusData, OrderStatus::TYPE_NEW, self::ORDER_STATUS_NEW);

        $orderStatusData->name = ['cs' => 'Vyřizuje se', 'en' => 'In progress'];
        $this->createOrderStatus($manager, $orderStatusData, OrderStatus::TYPE_IN_PROGRESS, self::ORDER_STATUS_IN_PROGRESS);

        $orderStatusData->name = ['cs' => 'Vyřízena', 'en' => 'Done'];
        $this->createOrderStatus($manager, $orderStatusData, OrderStatus::TYPE_DONE, self::ORDER_STATUS_DONE);

        $orderStatusData->name = ['cs' => 'Stornována', 'en' => 'Canceled'];
        $this->createOrderStatus($manager, $orderStatusData, OrderStatus::TYPE_CANCELED, self::ORDER_STATUS_CANCELED);
    }

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     * @param \Shopsys\FrameworkBundle\Model\Order\Status\OrderStatusData $orderStatusData
     * @param int $type
     * @param string|null $referenceName
     */
    private function createOrderStatus(
        ObjectManager $manager,
        OrderStatusData $orderStatusData,
        $type,
        $referenceName = null
    ) {
        $orderStatus = $this->orderStatusFactory->create($orderStatusData, $type);
        $manager->persist($orderStatus);
        $manager->flush($orderStatus);
        if ($referenceName !== null) {
            $this->addReference($referenceName, $orderStatus);
        }
    }
}
