<?php

namespace Shopsys\ShopBundle\Model\Order\Status\Grid;

use Shopsys\ShopBundle\Component\Grid\InlineEdit\AbstractGridInlineEdit;
use Shopsys\ShopBundle\Form\Admin\Order\Status\OrderStatusFormType;
use Shopsys\ShopBundle\Model\Order\Status\OrderStatusData;
use Shopsys\ShopBundle\Model\Order\Status\OrderStatusFacade;
use Symfony\Component\Form\FormFactory;

class OrderStatusInlineEdit extends AbstractGridInlineEdit
{
    /**
     * @var \Shopsys\ShopBundle\Model\Order\Status\OrderStatusFacade
     */
    private $orderStatusFacade;

    /**
     * @var \Symfony\Component\Form\FormFactory
     */
    private $formFactory;

    public function __construct(
        OrderStatusGridFactory $orderStatusGridFactory,
        OrderStatusFacade $orderStatusFacade,
        FormFactory $formFactory
    ) {
        parent::__construct($orderStatusGridFactory);
        $this->orderStatusFacade = $orderStatusFacade;
        $this->formFactory = $formFactory;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Order\Status\OrderStatusData $orderStatusData
     * @return int
     */
    protected function createEntityAndGetId($orderStatusData)
    {
        $orderStatus = $this->orderStatusFacade->create($orderStatusData);

        return $orderStatus->getId();
    }

    /**
     * @param int $orderStatusId
     * @param \Shopsys\ShopBundle\Model\Order\Status\OrderStatusData $orderStatusData
     */
    protected function editEntity($orderStatusId, $orderStatusData)
    {
        $this->orderStatusFacade->edit($orderStatusId, $orderStatusData);
    }

    /**
     * @param int|null $orderStatusId
     * @return \Symfony\Component\Form\FormInterface
     */
    public function getForm($orderStatusId)
    {
        $orderStatusData = new OrderStatusData();

        if ($orderStatusId !== null) {
            $orderStatus = $this->orderStatusFacade->getById((int)$orderStatusId);
            $orderStatusData->setFromEntity($orderStatus);
        }

        return $this->formFactory->create(OrderStatusFormType::class, $orderStatusData);
    }

    /**
     * @return string
     */
    public function getServiceName()
    {
        return self::class;
    }
}
