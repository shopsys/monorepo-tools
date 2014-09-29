<?php

namespace SS6\ShopBundle\Model\Order\Mail;

use SS6\ShopBundle\Model\Mail\MailTemplateFacade;
use SS6\ShopBundle\Model\Order\Order;
use SS6\ShopBundle\Model\Order\Mail\OrderMailService;
use SS6\ShopBundle\Model\Order\Status\OrderStatus;
use SS6\ShopBundle\Model\Order\Status\OrderStatusRepository;
use Swift_Mailer;

class OrderMailFacade {

	const STATUS_NAME_PREFIX = 'order_status_';
	
	/**
	 * @var \Swift_Mailer
	 */
	private $mailer;

	/**
	 * @var \SS6\ShopBundle\Model\Mail\MailTemplateFacade
	 */
	private $mailTemplateFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Order\Mail\OrderMailService
	 */
	private $orderMailService;

	/**
	 * @var \SS6\ShopBundle\Model\Order\Status\OrderStatusRepository
	 */
	private $orderStatusRepository;

	/**
	 * @param \Swift_Mailer $mailer
	 * @param \SS6\ShopBundle\Model\Mail\MailTemplateFacade $mailTemplateFacade
	 * @param \SS6\ShopBundle\Model\Order\Mail\OrderMailService $orderMailService
	 * @param \SS6\ShopBundle\Model\Order\Status\OrderStatusRepository $orderStatusRepository
	 */
	public function __construct(
		Swift_Mailer $mailer,
		MailTemplateFacade $mailTemplateFacade,
		OrderMailService $orderMailService,
		OrderStatusRepository $orderStatusRepository
	) {
		$this->mailer = $mailer;
		$this->mailTemplateFacade = $mailTemplateFacade;
		$this->orderMailService = $orderMailService;
		$this->orderStatusRepository = $orderStatusRepository;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Order $order
	 * @throws \SS6\ShopBundle\Model\Order\Mail\Exception\SendMailFailedException
	 */
	public function sendEmail(Order $order) {
		$mailTemplate = $this->getMailTemplateByStatus($order->getStatus());
		$message = $this->orderMailService->getMessageByOrder($order, $mailTemplate);

		$failedRecipients = array();
		$successSend = $this->mailer->send($message, $failedRecipients);
		if (!$successSend && count($failedRecipients) > 0) {
			throw new \SS6\ShopBundle\Model\Order\Mail\Exception\SendMailFailedException($failedRecipients);
		}
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Mail\OrderStatus $orderStatus
	 * @return \SS6\ShopBundle\Model\Mail\MailTemplate
	 */
	public function getMailTemplateByStatus(OrderStatus $orderStatus) {
		$templateName = $this->orderMailService->getMailTemplateNameByStatus($orderStatus);

		return $this->mailTemplateFacade->get($templateName);
	}

	/**
	 * @return \SS6\ShopBundle\Model\Mail\MailTemplate[]
	 */
	public function getAllOrderStatusMailTemplates() {
		$mailTemplates = array();
		foreach ($this->orderStatusRepository->findAll() as $orderStatus) {
			$mailTemplates[] = $this->getMailTemplateByStatus($orderStatus);
		}

		return $mailTemplates;
	}

	/**
	 * @return array
	 */
	public function getNamesByMailTemplateName(){
		$orderStatuses = $this->orderStatusRepository->findAll();
		foreach ($orderStatuses as $orderStatus) {
			$orderStatusNames[$this::STATUS_NAME_PREFIX . $orderStatus->getId()] = $orderStatus->getName();
		}
		
		return $orderStatusNames;
	}
}
