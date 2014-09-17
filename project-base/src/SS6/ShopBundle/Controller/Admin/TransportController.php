<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Model\AdminNavigation\MenuItem;
use SS6\ShopBundle\Model\Transport\Transport;
use SS6\ShopBundle\Model\Transport\TransportData;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class TransportController extends Controller {

	/**
	 * @Route("/transport/new/")
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function newAction(Request $request) {
		$flashMessageTwig = $this->get('ss6.shop.flash_message.twig_sender.admin');
		/* @var $flashMessageTwig \SS6\ShopBundle\Model\FlashMessage\TwigSender */
		$transportFormTypeFactory = $this->get('ss6.shop.form.admin.transport.transport_form_type_factory');
		/* @var $transportFormTypeFactory \SS6\ShopBundle\Form\Admin\Transport\TransportFormTypeFactory */

		$transportData = new TransportData();
		$form = $this->createForm($transportFormTypeFactory->create(), $transportData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$transportEditFacade = $this->get('ss6.shop.transport.transport_edit_facade');
			/* @var $transportEditFacade \SS6\ShopBundle\Model\Transport\TransportEditFacade */
			$transport = $transportEditFacade->create($transportData);
			
			$flashMessageTwig->addSuccess('Byla vytvořena doprava'
					. ' <strong><a href="{{ url }}">{{ name }}</a></strong>', array(
				'name' => $transport->getName(),
				'url' => $this->generateUrl('admin_transport_edit', array('id' => $transport->getId())),
			));
			return $this->redirect($this->generateUrl('admin_transportandpayment_list'));
		}

		if ($form->isSubmitted() && !$form->isValid()) {
			$flashMessageTwig->addError('Prosím zkontrolujte si správnost vyplnění všech údajů');
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
		$flashMessageTwig = $this->get('ss6.shop.flash_message.twig_sender.admin');
		/* @var $flashMessageTwig \SS6\ShopBundle\Model\FlashMessage\TwigSender */
		$transportEditFacade = $this->get('ss6.shop.transport.transport_edit_facade');
		/* @var $transportEditFacade \SS6\ShopBundle\Model\Transport\TransportEditFacade */
		$transportFormTypeFactory = $this->get('ss6.shop.form.admin.transport.transport_form_type_factory');
		/* @var $transportFormTypeFactory \SS6\ShopBundle\Form\Admin\Transport\TransportFormTypeFactory */
		$transportDetailFactory = $this->get('ss6.shop.transport.transport_detail_factory');
		/* @var $transportDetailFactory \SS6\ShopBundle\Model\Transport\Detail\Factory */
		
		$transport = $transportEditFacade->getById($id);
		/* @var $transport \SS6\ShopBundle\Model\Transport\Transport */

		$transportData = new TransportData();
		$transportData->setFromEntity($transport);

		$form = $this->createForm($transportFormTypeFactory->create(), $transportData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$transportEditFacade->edit($transport, $transportData);

			$flashMessageTwig->addSuccess('Byla upravena doprava'
					. ' <strong><a href="{{ url }}">{{ name }}</a></strong>', array(
				'name' => $transport->getName(),
				'url' => $this->generateUrl('admin_transport_edit', array('id' => $transport->getId())),
			));
			return $this->redirect($this->generateUrl('admin_transportandpayment_list'));
		}

		if ($form->isSubmitted() && !$form->isValid()) {
			$flashMessageTwig->addError('Prosím zkontrolujte si správnost vyplnění všech údajů');
		}

		$breadcrumb = $this->get('ss6.shop.admin_navigation.breadcrumb');
		/* @var $breadcrumb \SS6\ShopBundle\Model\AdminNavigation\Breadcrumb */
		$breadcrumb->replaceLastItem(new MenuItem('Editace dopravy - ' . $transport->getName()));

		return $this->render('@SS6Shop/Admin/Content/Transport/edit.html.twig', array(
			'form' => $form->createView(),
			'transportDetail' => $transportDetailFactory->createDetailForTransport($transport),
		));
	}
	
	/**
	 * @Route("/transport/delete/{id}", requirements={"id" = "\d+"})
	 * @param int $id
	 */
	public function deleteAction($id) {
		$flashMessageTwig = $this->get('ss6.shop.flash_message.twig_sender.admin');
		/* @var $flashMessageTwig \SS6\ShopBundle\Model\FlashMessage\TwigSender */
		$transportEditFacade = $this->get('ss6.shop.transport.transport_edit_facade');
		/* @var $transportEditFacade \SS6\ShopBundle\Model\Transport\TransportEditFacade */

		$transportName = $transportEditFacade->getById($id)->getName();
		$transportEditFacade->deleteById($id);

		$flashMessageTwig->addSuccess('Doprava <strong>{{ name }}</strong> byla smazána', array(
			'name' => $transportName,
		));
		return $this->redirect($this->generateUrl('admin_transportandpayment_list'));
	}

	public function listAction() {
		$transportRepository = $this->get('ss6.shop.transport.transport_repository');
		/* @var $transportRepository \SS6\ShopBundle\Model\Transport\TransportRepository */
		$transportDetailFactory = $this->get('ss6.shop.transport.transport_detail_factory');
		/* @var $transportDetailFactory \SS6\ShopBundle\Model\Transport\Detail\Factory */
		
		$transports = $transportRepository->findAll();
		$transportDetails = $transportDetailFactory->createDetailsForTransports($transports);
		
		return $this->render('@SS6Shop/Admin/Content/Transport/list.html.twig', array(
			'transportDetails' => $transportDetails,
		));
	}

}
