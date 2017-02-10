<?php

namespace Shopsys\ShopBundle\Model\Order\Status\Grid;

use Shopsys\ShopBundle\Component\Grid\InlineEdit\AbstractGridInlineEdit;
use Shopsys\ShopBundle\Form\Admin\Order\Status\OrderStatusFormType;
use Shopsys\ShopBundle\Model\Order\Status\Grid\OrderStatusGridFactory;
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
     * @param \Symfony\Component\Form\FormFactory $formFactory
     * @param \Shopsys\ShopBundle\Model\Order\Status\Grid\OrderStatusGridFactory $orderStatusGridFactory
     * @param \Shopsys\ShopBundle\Model\Order\Status\OrderStatusFacade $orderStatusFacade
     */
    public function __construct(
        FormFactory $formFactory,
        OrderStatusGridFactory $orderStatusGridFactory,
        OrderStatusFacade $orderStatusFacade
    ) {
        $this->orderStatusFacade = $orderStatusFacade;

        parent::__construct($formFactory, $orderStatusGridFactory);
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Order\Status\OrderStatusData $orderStatusData
     * @return int
     */
    protected function createEntityAndGetId($orderStatusData) {
        $orderStatus = $this->orderStatusFacade->create($orderStatusData);

        return $orderStatus->getId();
    }

    /**
     * @param int $orderStatusId
     * @param \Shopsys\ShopBundle\Model\Order\Status\OrderStatusData $orderStatusData
     */
    protected function editEntity($orderStatusId, $orderStatusData) {
        $this->orderStatusFacade->edit($orderStatusId, $orderStatusData);
    }

    /**
     * @param int $orderStatusId
     * @return \Shopsys\ShopBundle\Model\Order\Status\OrderStatusData
     */
    protected function getFormDataObject($orderStatusId = null) {
        $orderStatusData = new OrderStatusData();

        if ($orderStatusId !== null) {
            $orderStatusId = (int)$orderStatusId;
            $orderStatus = $this->orderStatusFacade->getById($orderStatusId);
            $orderStatusData->setFromEntity($orderStatus);
        }

        return $orderStatusData;
    }

    /**
     * @param int $rowId
     * @return \Shopsys\ShopBundle\Form\Admin\Order\Status\OrderStatusFormType
     */
    protected function getFormType($rowId) {
        return new OrderStatusFormType();
    }
}
