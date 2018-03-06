<?php

namespace Shopsys\FrameworkBundle\Model\Order\Status;

use Shopsys\FrameworkBundle\Model\Mail\MailTemplateData;
use Shopsys\FrameworkBundle\Model\Order\Mail\OrderMailService;

class OrderStatusMailTemplateService
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\Mail\OrderMailService
     */
    private $orderMailService;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Mail\OrderMailService $orderMailService
     */
    public function __construct(OrderMailService $orderMailService)
    {
        $this->orderMailService = $orderMailService;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Mail\MailTemplate[] $mailTemplates
     * @param \Shopsys\FrameworkBundle\Model\Order\Status\OrderStatus $orderStatus
     * @return \Shopsys\FrameworkBundle\Model\Mail\MailTemplate|null
     */
    private function getMailTemplateByOrderStatus(array $mailTemplates, OrderStatus $orderStatus)
    {
        foreach ($mailTemplates as $mailTemplate) {
            if ($mailTemplate->getName() === $this->orderMailService->getMailTemplateNameByStatus($orderStatus)) {
                return $mailTemplate;
            }
        }

        return null;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Status\OrderStatus[] $orderStatuses
     * @param \Shopsys\FrameworkBundle\Model\Mail\MailTemplate[] $mailTemplates
     * @return \Shopsys\FrameworkBundle\Model\Mail\MailTemplateData[]
     */
    public function getOrderStatusMailTemplatesData(array $orderStatuses, array $mailTemplates)
    {
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
     * @param \Shopsys\FrameworkBundle\Model\Order\Status\OrderStatus[] $orderStatuses
     * @param \Shopsys\FrameworkBundle\Model\Mail\MailTemplate[] $mailTemplates
     * @return \Shopsys\FrameworkBundle\Model\Mail\MailTemplate[]
     */
    public function getFilteredOrderStatusMailTemplatesIndexedByOrderStatusId(array $orderStatuses, array $mailTemplates)
    {
        $orderStatusMailTemplates = [];
        foreach ($orderStatuses as $orderStatus) {
            $orderStatusMailTemplates[$orderStatus->getId()] = $this->getMailTemplateByOrderStatus($mailTemplates, $orderStatus);
        }

        return $orderStatusMailTemplates;
    }
}
