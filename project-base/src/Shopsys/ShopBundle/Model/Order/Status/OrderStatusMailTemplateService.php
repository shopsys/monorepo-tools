<?php

namespace Shopsys\ShopBundle\Model\Order\Status;

use Shopsys\ShopBundle\Model\Mail\MailTemplate;
use Shopsys\ShopBundle\Model\Mail\MailTemplateData;
use Shopsys\ShopBundle\Model\Order\Mail\OrderMailService;

class OrderStatusMailTemplateService
{
    /**
     * @var \Shopsys\ShopBundle\Model\Order\Mail\OrderMailService
     */
    private $orderMailService;

    /**
     * @param \Shopsys\ShopBundle\Model\Order\Mail\OrderMailService $orderMailService
     */
    public function __construct(OrderMailService $orderMailService) {
        $this->orderMailService = $orderMailService;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Mail\MailTemplate[] $mailTemplates
     * @param \Shopsys\ShopBundle\Model\Order\Status\OrderStatus $orderStatus
     * @return \Shopsys\ShopBundle\Model\Mail\MailTemplate|null
     */
    private function getMailTemplateByOrderStatus(array $mailTemplates, OrderStatus $orderStatus) {
        foreach ($mailTemplates as $mailTemplate) {
            if ($mailTemplate->getName() === $this->orderMailService->getMailTemplateNameByStatus($orderStatus)) {
                return $mailTemplate;
            }
        }

        return null;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Order\Status\OrderStatus[] $orderStatuses
     * @param \Shopsys\ShopBundle\Model\Mail\MailTemplate[] $mailTemplates
     * @return \Shopsys\ShopBundle\Model\Mail\MailTemplateData[]
     */
    public function getOrderStatusMailTemplatesData(array $orderStatuses, array $mailTemplates) {
        $orderStatusMailTemplatesData = [];
        foreach ($orderStatuses as $orderStatus) {
            $orderStatusMailTemplateData = new MailTemplateData();

            $mailTemplate = $this->getMailTemplateByOrderStatus($mailTemplates, $orderStatus);
            if ($mailTemplate !== null) {
                $orderStatusMailTemplateData->setFromEntity($mailTemplate);
            }
            $orderStatusMailTemplateData->name = $this->orderMailService->getMailTemplateNameByStatus($orderStatus);

            $orderStatusMailTemplatesData[$orderStatus->getId()] = $orderStatusMailTemplateData;
        }

        return $orderStatusMailTemplatesData;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Order\Status\OrderStatus[] $orderStatuses
     * @param \Shopsys\ShopBundle\Model\Mail\MailTemplate[] $mailTemplates
     * @return \Shopsys\ShopBundle\Model\Mail\MailTemplate[]
     */
    public function getFilteredOrderStatusMailTemplatesIndexedByOrderStatusId(array $orderStatuses, array $mailTemplates) {
        $orderStatusMailTemplates = [];
        foreach ($orderStatuses as $orderStatus) {
            $orderStatusMailTemplates[$orderStatus->getId()] = $this->getMailTemplateByOrderStatus($mailTemplates, $orderStatus);
        }

        return $orderStatusMailTemplates;
    }
}
