<?php

namespace Shopsys\FrameworkBundle\Model\Mail;

use Shopsys\FrameworkBundle\Model\Order\Mail\OrderMail;

class MailTemplateDataFactory implements MailTemplateDataFactoryInterface
{
    /**
     * @return \Shopsys\FrameworkBundle\Model\Mail\MailTemplateData
     */
    public function create(): MailTemplateData
    {
        return new MailTemplateData();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Mail\MailTemplate $mailTemplate
     * @return \Shopsys\FrameworkBundle\Model\Mail\MailTemplateData
     */
    public function createFromMailTemplate(MailTemplate $mailTemplate): MailTemplateData
    {
        $mailTemplateData = new MailTemplateData();
        $this->fillFromMailTemplate($mailTemplateData, $mailTemplate);

        return $mailTemplateData;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Mail\MailTemplateData $mailTemplateData
     * @param \Shopsys\FrameworkBundle\Model\Mail\MailTemplate $mailTemplate
     */
    protected function fillFromMailTemplate(MailTemplateData $mailTemplateData, MailTemplate $mailTemplate)
    {
        $mailTemplateData->name = $mailTemplate->getName();
        $mailTemplateData->bccEmail = $mailTemplate->getBccEmail();
        $mailTemplateData->subject = $mailTemplate->getSubject();
        $mailTemplateData->body = $mailTemplate->getBody();
        $mailTemplateData->sendMail = $mailTemplate->isSendMail();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Status\OrderStatus[] $orderStatuses
     * @param \Shopsys\FrameworkBundle\Model\Mail\MailTemplate[] $mailTemplates
     * @return \Shopsys\FrameworkBundle\Model\Mail\MailTemplateData[]
     */
    public function createFromOrderStatuses(array $orderStatuses, array $mailTemplates): array
    {
        $orderStatusMailTemplatesData = [];
        foreach ($orderStatuses as $orderStatus) {
            $mailTemplate = OrderMail::findMailTemplateForOrderStatus($mailTemplates, $orderStatus);
            if ($mailTemplate !== null) {
                $orderStatusMailTemplateData = $this->createFromMailTemplate($mailTemplate);
            } else {
                $orderStatusMailTemplateData = $this->create();
            }
            $orderStatusMailTemplateData->name = OrderMail::getMailTemplateNameByStatus($orderStatus);

            $orderStatusMailTemplatesData[$orderStatus->getId()] = $orderStatusMailTemplateData;
        }

        return $orderStatusMailTemplatesData;
    }
}
