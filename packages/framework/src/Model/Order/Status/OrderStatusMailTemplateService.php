<?php

namespace Shopsys\FrameworkBundle\Model\Order\Status;

use Shopsys\FrameworkBundle\Model\Mail\MailTemplateDataFactoryInterface;
use Shopsys\FrameworkBundle\Model\Order\Mail\OrderMail;

class OrderStatusMailTemplateService
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Mail\MailTemplateDataFactoryInterface
     */
    private $mailTemplateDataFactory;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Mail\MailTemplateDataFactoryInterface $mailTemplateDataFactory
     */
    public function __construct(
        MailTemplateDataFactoryInterface $mailTemplateDataFactory
    ) {
        $this->mailTemplateDataFactory = $mailTemplateDataFactory;
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
            $mailTemplate = OrderMail::findMailTemplateForOrderStatus($mailTemplates, $orderStatus);
            if ($mailTemplate !== null) {
                $orderStatusMailTemplateData = $this->mailTemplateDataFactory->createFromMailTemplate($mailTemplate);
            } else {
                $orderStatusMailTemplateData = $this->mailTemplateDataFactory->create();
            }
            $orderStatusMailTemplateData->name = OrderMail::getMailTemplateNameByStatus($orderStatus);

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
            $orderStatusMailTemplates[$orderStatus->getId()] = OrderMail::findMailTemplateForOrderStatus($mailTemplates, $orderStatus);
        }

        return $orderStatusMailTemplates;
    }
}
