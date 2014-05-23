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
		$fileUpload = $this->get('ss6.shop.file_upload');
		/* @var $fileUpload \SS6\ShopBundle\Model\FileUpload\FileUpload */
		$flashMessage = $this->get('ss6.shop.flash_message.admin');
		/* @var $flashMessage \SS6\ShopBundle\Model\FlashMessage\FlashMessage */

		$transportData = new TransportFormData();
		$form = $this->createForm(new TransportFormType($fileUpload), $transportData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$transport = new Transport(
				$transportData->getName(),
				$transportData->getPrice(),
				$transportData->getDescription(),
				$transportData->isHidden()
			);
			$transport->setImageForUpload($transportData->getImage());
			
			$transportEditFacade = $this->get('ss6.shop.transport.transport_edit_facade');
			/* @var $transportEditFacade \SS6\ShopBundle\Model\Transport\TransportEditFacade */
			$transportEditFacade->create($transport);
			$flashMessage->addSuccess('Byla vytvořena doprava ' . $transport->getName());
			return $this->redirect($this->generateUrl('admin_transportandpayment_list'));
		} elseif ($form->isSubmitted()) {
			$flashMessage->addError('Prosím zkontrolujte si správnost vyplnění všech údajů.');
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
		$fileUpload = $this->get('ss6.shop.file_upload');
		/* @var $fileUpload \SS6\ShopBundle\Model\FileUpload\FileUpload */
		$flashMessage = $this->get('ss6.shop.flash_message.admin');
		/* @var $flashMessage \SS6\ShopBundle\Model\FlashMessage\FlashMessage */
		
		try {
			$transport = $transportEditFacade->getById($id);
			/* @var $transport \SS6\ShopBundle\Model\Transport\Transport */
			
			$formData = new TransportFormData();
			$formData->setId($transport->getId());
			$formData->setName($transport->getName());
			$formData->setPrice($transport->getPrice());
			$formData->setDescription($transport->getDescription());
			$formData->setHidden($transport->isHidden());
			
			$form = $this->createForm(new TransportFormType($fileUpload), $formData);
			$form->handleRequest($request);

			if ($form->isValid()) {
				$transportEditFacade->edit($transport, $formData);
				$flashMessage->addSuccess('Byla upravena doprava ' . $transport->getName());
				return $this->redirect($this->generateUrl('admin_transportandpayment_list'));
			} elseif ($form->isSubmitted()) {
				$flashMessage->addError('Prosím zkontrolujte si správnost vyplnění všech údajů.');
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
		$flashMessage = $this->get('ss6.shop.flash_message.admin');
		/* @var $flashMessage \SS6\ShopBundle\Model\FlashMessage\FlashMessage */
		
		try {
			$transportName = $transportEditFacade->getById($id)->getName();
			$transportEditFacade->deleteById($id);
			$flashMessage->addSuccess('Doprava ' . $transportName . ' byla odstraněna');
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
