<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Form\Admin\Transport\TransportFormData;
use SS6\ShopBundle\Form\Admin\Transport\TransportFormType;
use SS6\ShopBundle\Model\Transport\Transport;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class TransportController extends Controller {

	/**
	 * @Route("/transport/new/")
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function newAction(Request $request) {
		$formData = new TransportFormData();
		$form = $this->createForm(new TransportFormType(), $formData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$transport = new Transport(
				$formData->getName(), 
				$formData->getPrice(), 
				$formData->getDescription(), 
				$formData->isHidden()
			);
			
			$transportEditFacade = $this->get('ss6.shop.transport.transport_edit_facade');
			/* @var $transportEditFacade \SS6\ShopBundle\Model\Transport\TransportEditFacade */
			$transportEditFacade->create($transport);
			return $this->redirect($this->generateUrl('admin_transport_edit', array('id' => $transport->getId())));
		}

		return $this->render('@SS6Shop/Admin/Content/Transport/new.html.twig', array(
			'form' => $form->createView(),
		));
	}
	
	/**
	 * @Route("/transport/edit/{id}", requirements={"id" = "\d+"})
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param int $id
	 */
	public function editAction(Request $request, $id) {
		$transportEditFacade = $this->get('ss6.shop.transport.transport_edit_facade');
		/* @var $transportEditFacade \SS6\ShopBundle\Model\Transport\TransportEditFacade */
		
		try {
			$transport = $transportEditFacade->getById($id);
			/* @var $transport \SS6\ShopBundle\Model\Transport\Transport */
			
			$formData = new TransportFormData();
			$formData->setId($transport->getId());
			$formData->setName($transport->getName());
			$formData->setPrice($transport->getPrice());
			$formData->setDescription($transport->getDescription());
			$formData->setHidden($transport->isHidden());
			
			$form = $this->createForm(new TransportFormType(), $formData);
			$form->handleRequest($request);

			if ($form->isValid()) {
				$transport->setEdit(
					$formData->getName(), 
					$formData->getPrice(), 
					$formData->getDescription(), 
					$formData->isHidden()
				);
				$transportEditFacade->edit($transport);
				return $this->redirect($this->generateUrl('admin_transport_edit', array('id' => $id)));
			}
		} catch (\SS6\ShopBundle\Model\Transport\Exception\TransportNotFoundException $e) {
			throw $this->createNotFoundException($e->getMessage(), $e);
		}

		return $this->render('@SS6Shop/Admin/Content/Transport/edit.html.twig', array(
			'form' => $form->createView(),
			'transport' => $transport,
		));
	}
	
	/**
	 * @Route("/transport/delete/{id}", requirements={"id" = "\d+"})
	 * @param int $id
	 */
	public function deleteAction($id) {
		$transportEditFacade = $this->get('ss6.shop.transport.transport_edit_facade');
		/* @var $transportEditFacade \SS6\ShopBundle\Model\Transport\TransportEditFacade */
		
		try {
			$transportEditFacade->deleteById($id);
			return $this->redirect($this->generateUrl('admin_transportandpayment_list'));
		} catch (\SS6\ShopBundle\Model\Transport\Exception\TransportNotFoundException $e) {
			throw $this->createNotFoundException($e->getMessage(), $e);
		}
	}
	
	public function listAction() {
		$transportRepository = $this->get('ss6.shop.transport.transport_repository');
		/* @var $transportRepository \SS6\ShopBundle\Model\Transport\TransportRepository */
		$paymentRepository = $this->get('ss6.shop.payment.payment_repository');
		/* @var $paymentRepository \SS6\ShopBundle\Model\Payment\PaymentRepository */
		
		$allPayments = $paymentRepository->getAllWithTransports();
		$transports = $transportRepository->getAllDataWithVisibility($allPayments);
		
		return $this->render('@SS6Shop/Admin/Content/Transport/list.html.twig', array(
			'transports' => $transports,
		));
	}

}
