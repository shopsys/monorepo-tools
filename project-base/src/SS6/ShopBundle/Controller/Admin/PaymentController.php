<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Form\Admin\Payment\PaymentFormData;
use SS6\ShopBundle\Form\Admin\Payment\PaymentFormType;
use SS6\ShopBundle\Model\AdminNavigation\MenuItem;
use SS6\ShopBundle\Model\Payment\Payment;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class PaymentController extends Controller {

	/**
	 * @Route("/payment/new/")
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function newAction(Request $request) {
		$fileUpload = $this->get('ss6.shop.file_upload');
		/* @var $fileUpload \SS6\ShopBundle\Model\FileUpload\FileUpload */
		$transportRepository = $this->get('ss6.shop.transport.transport_repository');
		/* @var $transportRepository \SS6\ShopBundle\Model\Transport\TransportRepository */
		$allTransports = $transportRepository->findAll();

		$flashMessageText = $this->get('ss6.shop.flash_message.text_sender.admin');
		/* @var $flashMessageText \SS6\ShopBundle\Model\FlashMessage\TextSender */

		$paymentData = new PaymentFormData();
		$form = $this->createForm(new PaymentFormType($allTransports, $fileUpload), $paymentData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$transportRepository->findAll();
			$payment = new Payment(
				$paymentData->getName(),
				$paymentData->getPrice(),
				$paymentData->getDescription(),
				$paymentData->isHidden()
			);
			$payment->setImageForUpload($paymentData->getImage());

			$transports = $transportRepository->findAllByIds($paymentData->getTransports());
			$payment->setTransports($transports);

			$paymentEditFacade = $this->get('ss6.shop.payment.payment_edit_facade');
			/* @var $paymentEditFacade \SS6\ShopBundle\Model\Payment\PaymentEditFacade */
			$paymentEditFacade->create($payment);

			$flashMessageText->addSuccess('Byla vytvořena platba ' . $payment->getName());
			return $this->redirect($this->generateUrl('admin_transportandpayment_list'));
		}

		if ($form->isSubmitted() && !$form->isValid()) {
			$flashMessageText->addError('Prosím zkontrolujte si správnost vyplnění všech údajů');
		}

		return $this->render('@SS6Shop/Admin/Content/Payment/new.html.twig', array(
			'form' => $form->createView(),
		));
	}
	
	/**
	 * @Route("/payment/edit/{id}", requirements={"id" = "\d+"})
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param int $id
	 */
	public function editAction(Request $request, $id) {
		$transportRepository = $this->get('ss6.shop.transport.transport_repository');
		/* @var $transportRepository \SS6\ShopBundle\Model\Transport\TransportRepository */
		$paymentEditFacade = $this->get('ss6.shop.payment.payment_edit_facade');
		/* @var $paymentEditFacade \SS6\ShopBundle\Model\Payment\PaymentEditFacade */
		$flashMessageText = $this->get('ss6.shop.flash_message.text_sender.admin');
		/* @var $flashMessageText \SS6\ShopBundle\Model\FlashMessage\TextSender */
		$fileUpload = $this->get('ss6.shop.file_upload');
		/* @var $fileUpload \SS6\ShopBundle\Model\FileUpload\FileUpload */
		
		$allTransports = $transportRepository->findAll();

		/* @var $payment \SS6\ShopBundle\Model\Payment\Payment */
		$payment = $paymentEditFacade->getByIdWithTransports($id);

		$formData = new PaymentFormData();
		$formData->setId($payment->getId());
		$formData->setName($payment->getName());
		$formData->setPrice($payment->getPrice());
		$formData->setDescription($payment->getDescription());
		$formData->setHidden($payment->isHidden());

		$transports = array();
		foreach ($payment->getTransports() as $transport) {
			$transports[] = $transport->getId();
		}
		$formData->setTransports($transports);

		$form = $this->createForm(new PaymentFormType($allTransports, $fileUpload), $formData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$paymentEditFacade->edit($payment, $formData);

			$flashMessageText->addSuccess('Byla upravena platba ' . $payment->getName());
			return $this->redirect($this->generateUrl('admin_transportandpayment_list'));
		}

		if ($form->isSubmitted() && !$form->isValid()) {
			$flashMessageText->addError('Prosím zkontrolujte si správnost vyplnění všech údajů');
		}

		$breadcrumb = $this->get('ss6.shop.admin_navigation.breadcrumb');
		/* @var $breadcrumb \SS6\ShopBundle\Model\AdminNavigation\Breadcrumb */
		$breadcrumb->replaceLastItem(new MenuItem('Editace platby - ' . $payment->getName()));

		return $this->render('@SS6Shop/Admin/Content/Payment/edit.html.twig', array(
			'form' => $form->createView(),
			'payment' => $payment,
		));
	}
	
	/**
	 * @Route("/payment/delete/{id}", requirements={"id" = "\d+"})
	 * @param int $id
	 */
	public function deleteAction($id) {
		$paymentEditFacade = $this->get('ss6.shop.payment.payment_edit_facade');
		/* @var $paymentEditFacade \SS6\ShopBundle\Model\Payment\PaymentEditFacade */
		$flashMessageText = $this->get('ss6.shop.flash_message.text_sender.admin');
		/* @var $flashMessageText \SS6\ShopBundle\Model\FlashMessage\TextSender */
		
		$paymentName = $paymentEditFacade->getById($id)->getName();
		$paymentEditFacade->deleteById($id);
		$flashMessageText->addSuccess('Platba ' . $paymentName . ' byla odstraněna');
		
		return $this->redirect($this->generateUrl('admin_transportandpayment_list'));
	}
	
	public function listAction() {
		$paymentRepository = $this->get('ss6.shop.payment.payment_repository');
		/* @var $paymentRepository \SS6\ShopBundle\Model\Payment\PaymentRepository */
		$payments = $paymentRepository->findAll();
		
		return $this->render('@SS6Shop/Admin/Content/Payment/list.html.twig', array(
			'payments' => $payments,
		));
	}

}
