<?php

namespace SS6\AdminBundle\Controller;

use SS6\AdminBundle\Form\Payment\PaymentFormData;
use SS6\AdminBundle\Form\Payment\PaymentFormType;
use SS6\CoreBundle\Model\Payment\Entity\Payment;
use SS6\CoreBundle\Model\Payment\Exception\PaymentNotFoundException;
use SS6\CoreBundle\Model\Payment\Facade\PaymentEditFacade;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class PaymentController extends Controller {

	public function newAction(Request $request) {
		$formData = new PaymentFormData();
		$form = $this->createForm(new PaymentFormType(), $formData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$payment = new Payment(
				$formData->getName(), 
				$formData->getPrice(), 
				$formData->getDescription(), 
				$formData->isHidden()
			);
			
			$paymentEditFacade = $this->get('ss6.core.payment.payment_edit_facade');
			/* @var $paymentEditFacade PaymentEditFacade */
			$paymentEditFacade->create($payment);
			return $this->redirect($this->generateUrl('admin_payment_edit', array('id' => $payment->getId())));
		}

		return $this->render('SS6AdminBundle:Content:Payment/new.html.twig', array(
			'form' => $form->createView(),
		));
	}
	
	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param int $id
	 */
	public function editAction(Request $request, $id) {
		$paymentEditFacade = $this->get('ss6.core.payment.payment_edit_facade');
		/* @var $paymentEditFacade PaymentEditFacade */
		
		try {
			$payment = $paymentEditFacade->getById($id);
			/* @var $payment Payment */
			
			$formData = new PaymentFormData();
			$formData->setId($payment->getId());
			$formData->setName($payment->getName());
			$formData->setPrice($payment->getPrice());
			$formData->setDescription($payment->getDescription());
			$formData->setHidden($payment->isHidden());
			
			$form = $this->createForm(new PaymentFormType(), $formData);
			$form->handleRequest($request);

			if ($form->isValid()) {
				$payment->setEdit(
					$formData->getName(), 
					$formData->getPrice(), 
					$formData->getDescription(), 
					$formData->isHidden()
				);
				$paymentEditFacade->edit($payment);
				return $this->redirect($this->generateUrl('admin_payment_edit', array('id' => $id)));
			}
		} catch (PaymentNotFoundException $e) {
			throw $this->createNotFoundException($e->getMessage(), $e);
		}

		return $this->render('SS6AdminBundle:Content:Payment/edit.html.twig', array(
			'form' => $form->createView(),
			'payment' => $payment,
		));
	}
	
	/**
	 * @param int $id
	 */
	public function deleteAction($id) {
		$paymentEditFacade = $this->get('ss6.core.payment.payment_edit_facade');
		/* @var $paymentEditFacade PaymentEditFacade */
		
		try {
			$paymentEditFacade->deleteById($id);
			return $this->redirect($this->generateUrl('admin_transport_and_payment_list'));
		} catch (PaymentNotFoundException $e) {
			throw $this->createNotFoundException($e->getMessage(), $e);
		}
	}

}
