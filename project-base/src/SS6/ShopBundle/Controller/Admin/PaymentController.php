<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Model\AdminNavigation\MenuItem;
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
		$paymentFormTypeFactory = $this->get('ss6.shop.form.admin.payment.payment_form_type_factory');
		/* @var $paymentFormTypeFactory \SS6\ShopBundle\Form\Admin\Payment\PaymentFormTypeFactory */
		$vatFacade = $this->get('ss6.shop.pricing.vat.vat_facade');
		/* @var $vatFacade \SS6\ShopBundle\Model\Pricing\Vat\VatFacade */

		$paymentData = new PaymentData();
		$paymentData->setVat($vatFacade->getDefaultVat());
		
		$form = $this->createForm($paymentFormTypeFactory->create(), $paymentData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$paymentEditFacade = $this->get('ss6.shop.payment.payment_edit_facade');
			/* @var $paymentEditFacade \SS6\ShopBundle\Model\Payment\PaymentEditFacade */
			$payment = $paymentEditFacade->create($paymentData);

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
		$paymentDetailFactory = $this->get('ss6.shop.payment.payment_detail_factory');
		/* @var $paymentDetailFactory \SS6\ShopBundle\Model\Payment\Detail\Factory */

		$payment = $paymentEditFacade->getByIdWithTransports($id);
		/* @var $payment \SS6\ShopBundle\Model\Payment\Payment */

		$paymentData = new PaymentData();
		$paymentData->setFromEntity($payment);

		$form = $this->createForm($paymentFormTypeFactory->create(), $paymentData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$paymentEditFacade->edit($payment, $paymentData);

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
			'paymentDetail' => $paymentDetailFactory->createDetailForPayment($payment),
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
		$paymentDetailFactory = $this->get('ss6.shop.payment.payment_detail_factory');
		/* @var $paymentDetailFactory \SS6\ShopBundle\Model\Payment\Detail\Factory */

		$payments = $paymentRepository->findAll();
		$paymentDetails = $paymentDetailFactory->createDetailsForPayments($payments);
		
		return $this->render('@SS6Shop/Admin/Content/Payment/list.html.twig', array(
			'paymentDetails' => $paymentDetails,
		));
	}

}
