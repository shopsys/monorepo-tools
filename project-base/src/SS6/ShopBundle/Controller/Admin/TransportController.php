<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Model\AdminNavigation\MenuItem;
use SS6\ShopBundle\Model\Grid\QueryBuilderWithRowManipulatorDataSource;
use SS6\ShopBundle\Model\Transport\TransportData;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class TransportController extends Controller {

	/**
	 * @Route("/transport/new/")
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function newAction(Request $request) {
		$flashMessageSender = $this->get('ss6.shop.flash_message.sender.admin');
		/* @var $flashMessageSender \SS6\ShopBundle\Model\FlashMessage\FlashMessageSender */
		$transportFormTypeFactory = $this->get('ss6.shop.form.admin.transport.transport_form_type_factory');
		/* @var $transportFormTypeFactory \SS6\ShopBundle\Form\Admin\Transport\TransportFormTypeFactory */
		$vatFacade = $this->get('ss6.shop.pricing.vat.vat_facade');
		/* @var $vatFacade \SS6\ShopBundle\Model\Pricing\Vat\VatFacade */

		$transportData = new TransportData();
		$transportData->setVat($vatFacade->getDefaultVat());

		$form = $this->createForm($transportFormTypeFactory->create(), $transportData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$transportEditFacade = $this->get('ss6.shop.transport.transport_edit_facade');
			/* @var $transportEditFacade \SS6\ShopBundle\Model\Transport\TransportEditFacade */
			$transport = $transportEditFacade->create($transportData);

			$flashMessageSender->addSuccessTwig('Byla vytvořena doprava'
					. ' <strong><a href="{{ url }}">{{ name }}</a></strong>', array(
				'name' => $transport->getName(),
				'url' => $this->generateUrl('admin_transport_edit', array('id' => $transport->getId())),
			));
			return $this->redirect($this->generateUrl('admin_transportandpayment_list'));
		}

		if ($form->isSubmitted() && !$form->isValid()) {
			$flashMessageSender->addErrorTwig('Prosím zkontrolujte si správnost vyplnění všech údajů');
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
		$flashMessageSender = $this->get('ss6.shop.flash_message.sender.admin');
		/* @var $flashMessageSender \SS6\ShopBundle\Model\FlashMessage\FlashMessageSender */
		$transportEditFacade = $this->get('ss6.shop.transport.transport_edit_facade');
		/* @var $transportEditFacade \SS6\ShopBundle\Model\Transport\TransportEditFacade */
		$transportFormTypeFactory = $this->get('ss6.shop.form.admin.transport.transport_form_type_factory');
		/* @var $transportFormTypeFactory \SS6\ShopBundle\Form\Admin\Transport\TransportFormTypeFactory */
		$transportDetailFactory = $this->get('ss6.shop.transport.transport_detail_factory');
		/* @var $transportDetailFactory \SS6\ShopBundle\Model\Transport\Detail\Factory */

		$transport = $transportEditFacade->getById($id);
		/* @var $transport \SS6\ShopBundle\Model\Transport\Transport */
		$transportDomains = $transportEditFacade->getTransportDomainsByTransport($transport);

		$transportData = new TransportData();
		$transportData->setFromEntity($transport, $transportDomains);

		$form = $this->createForm($transportFormTypeFactory->create(), $transportData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$transportEditFacade->edit($transport, $transportData);

			$flashMessageSender->addSuccessTwig('Byla upravena doprava'
					. ' <strong><a href="{{ url }}">{{ name }}</a></strong>', array(
				'name' => $transport->getName(),
				'url' => $this->generateUrl('admin_transport_edit', array('id' => $transport->getId())),
			));
			return $this->redirect($this->generateUrl('admin_transportandpayment_list'));
		}

		if ($form->isSubmitted() && !$form->isValid()) {
			$flashMessageSender->addError('Prosím zkontrolujte si správnost vyplnění všech údajů');
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
		$flashMessageSender = $this->get('ss6.shop.flash_message.sender.admin');
		/* @var $flashMessageSender \SS6\ShopBundle\Model\FlashMessage\FlashMessageSender */
		$transportEditFacade = $this->get('ss6.shop.transport.transport_edit_facade');
		/* @var $transportEditFacade \SS6\ShopBundle\Model\Transport\TransportEditFacade */

		$transportName = $transportEditFacade->getById($id)->getName();
		$transportEditFacade->deleteById($id);

		$flashMessageSender->addSuccessTwig('Doprava <strong>{{ name }}</strong> byla smazána', array(
			'name' => $transportName,
		));
		return $this->redirect($this->generateUrl('admin_transportandpayment_list'));
	}

	public function listAction() {
		$transportGridFactory = $this->get('ss6.shop.transport.grid.transport_grid_factory');
		/* @var $transportGridFactory \SS6\ShopBundle\Model\Transport\Grid\TransportGridFactory */

		$grid = $transportGridFactory->create();

		return $this->render('@SS6Shop/Admin/Content/Transport/list.html.twig', array(
			'gridView' => $grid->createView(),
		));
	}

}
