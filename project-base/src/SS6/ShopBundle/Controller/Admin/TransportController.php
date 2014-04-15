<?php

namespace SS6\ShopBundle\Controller\Admin;

use SS6\ShopBundle\Form\Admin\Transport\TransportFormData;
use SS6\ShopBundle\Form\Admin\Transport\TransportFormType;
use SS6\ShopBundle\Model\Transport\Transport;
use SS6\ShopBundle\Model\Transport\TransportEditFacade;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class TransportController extends Controller {

	/**
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
			
			$transportEditFacade = $this->get('ss6.core.transport.transport_edit_facade');
			/* @var $transportEditFacade TransportEditFacade */
			$transportEditFacade->create($transport);
			return $this->redirect($this->generateUrl('admin_transport_edit', array('id' => $transport->getId())));
		}

		return $this->render('@SS6Shop/Admin/Content/Transport/new.html.twig', array(
			'form' => $form->createView(),
		));
	}
	
	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param int $id
	 */
	public function editAction(Request $request, $id) {
		$transportEditFacade = $this->get('ss6.core.transport.transport_edit_facade');
		/* @var $transportEditFacade TransportEditFacade */
		
		try {
			$transport = $transportEditFacade->getById($id);
			/* @var $transport Transport */
			
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
	 * @param int $id
	 */
	public function deleteAction($id) {
		$transportEditFacade = $this->get('ss6.core.transport.transport_edit_facade');
		/* @var $transportEditFacade TransportEditFacade */
		
		try {
			$transportEditFacade->deleteById($id);
			return $this->redirect($this->generateUrl('admin_transport_and_payment_list'));
		} catch (\SS6\ShopBundle\Model\Transport\Exception\TransportNotFoundException $e) {
			throw $this->createNotFoundException($e->getMessage(), $e);
		}
	}

}
