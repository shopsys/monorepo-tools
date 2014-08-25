<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Model\AdminNavigation\MenuItem;
use SS6\ShopBundle\Model\Payment\Payment;
use SS6\ShopBundle\Model\Payment\PaymentData;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class PaymentController extends Controller {

	/**
	 * @Route("/payment/new/")
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function newAction(Request $request) {
		$flashMessageTwig = $this->get('ss6.shop.flash_message.twig_sender.admin');
		/* @var $flashMessageTwig \SS6\ShopBundle\Model\FlashMessage\TwigSender */
		$transportRepository = $this->get('ss6.shop.transport.transport_repository');
		/* @var $transportRepository \SS6\ShopBundle\Model\Transport\TransportRepository */
		$paymentFormTypeFactory = $this->get('ss6.shop.form.admin.payment.payment_form_type_factory');
		/* @var $paymentFormTypeFactory \SS6\ShopBundle\Form\Admin\Payment\PaymentFormTypeFactory */

		$paymentData = new PaymentData();
		$form = $this->createForm($paymentFormTypeFactory->create(), $paymentData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$transportRepository->findAll();
			$payment = new Payment($paymentData);
			$payment->setImageForUpload($paymentData->getImage());

			$transports = $transportRepository->findAllByIds($paymentData->getTransports());
			$payment->setTransports($transports);

			$paymentEditFacade = $this->get('ss6.shop.payment.payment_edit_facade');
			/* @var $paymentEditFacade \SS6\ShopBundle\Model\Payment\PaymentEditFacade */
			$paymentEditFacade->create($payment);

			$flashMessageTwig->addSuccess('Byla vytvořena platba'
					. ' <strong><a href="{{ url }}">{{ name }}</a></strong>', array(
				'name' => $payment->getName(),
				'url' => $this->generateUrl('admin_payment_edit', array('id' => $payment->getId())),
			));
			return $this->redirect($this->generateUrl('admin_transportandpayment_list'));
		}

		if ($form->isSubmitted() && !$form->isValid()) {
			$flashMessageTwig->addError('Prosím zkontrolujte si správnost vyplnění všech údajů');
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
		$flashMessageTwig = $this->get('ss6.shop.flash_message.twig_sender.admin');
		/* @var $flashMessageTwig \SS6\ShopBundle\Model\FlashMessage\TwigSender */
		$paymentEditFacade = $this->get('ss6.shop.payment.payment_edit_facade');
		/* @var $paymentEditFacade \SS6\ShopBundle\Model\Payment\PaymentEditFacade */
		$paymentFormTypeFactory = $this->get('ss6.shop.form.admin.payment.payment_form_type_factory');
		/* @var $paymentFormTypeFactory \SS6\ShopBundle\Form\Admin\Payment\PaymentFormTypeFactory */

		$payment = $paymentEditFacade->getByIdWithTransports($id);
		/* @var $payment \SS6\ShopBundle\Model\Payment\Payment */

		$formData = new PaymentData();
		$formData->setId($payment->getId());
		$formData->setName($payment->getName());
		$formData->setPrice($payment->getPrice());
		$formData->setVat($payment->getVat());
		$formData->setDescription($payment->getDescription());
		$formData->setHidden($payment->isHidden());

		$transports = array();
		foreach ($payment->getTransports() as $transport) {
			$transports[] = $transport->getId();
		}
		$formData->setTransports($transports);

		$form = $this->createForm($paymentFormTypeFactory->create(), $formData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$paymentEditFacade->edit($payment, $formData);

			$flashMessageTwig->addSuccess('Byla upravena platba'
					. ' <strong><a href="{{ url }}">{{ name }}</a></strong>', array(
				'name' => $payment->getName(),
				'url' => $this->generateUrl('admin_payment_edit', array('id' => $payment->getId())),
			));
			return $this->redirect($this->generateUrl('admin_transportandpayment_list'));
		}

		if ($form->isSubmitted() && !$form->isValid()) {
			$flashMessageTwig->addError('Prosím zkontrolujte si správnost vyplnění všech údajů');
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
		$flashMessageTwig = $this->get('ss6.shop.flash_message.twig_sender.admin');
		/* @var $flashMessageTwig \SS6\ShopBundle\Model\FlashMessage\TwigSender */
		$paymentEditFacade = $this->get('ss6.shop.payment.payment_edit_facade');
		/* @var $paymentEditFacade \SS6\ShopBundle\Model\Payment\PaymentEditFacade */
		
		$paymentName = $paymentEditFacade->getById($id)->getName();
		$paymentEditFacade->deleteById($id);

		$flashMessageTwig->addSuccess('Platba <strong>{{ name }}</strong> byla smazána', array(
			'name' => $paymentName,
		));
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
