<?php

namespace SS6\ShopBundle\Controller\Admin;

use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Component\Router\Security\Annotation\CsrfProtection;
use SS6\ShopBundle\Component\Translation\Translator;
use SS6\ShopBundle\Controller\Admin\BaseController;
use SS6\ShopBundle\Form\Admin\Transport\TransportEditFormTypeFactory;
use SS6\ShopBundle\Model\AdminNavigation\Breadcrumb;
use SS6\ShopBundle\Model\AdminNavigation\MenuItem;
use SS6\ShopBundle\Model\Pricing\Currency\CurrencyFacade;
use SS6\ShopBundle\Model\Transport\Detail\TransportDetailFactory;
use SS6\ShopBundle\Model\Transport\Grid\TransportGridFactory;
use SS6\ShopBundle\Model\Transport\TransportEditDataFactory;
use SS6\ShopBundle\Model\Transport\TransportEditFacade;
use Symfony\Component\HttpFoundation\Request;

class TransportController extends BaseController {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Form\Admin\Transport\TransportEditFormTypeFactory
	 */
	private $transportEditFormTypeFactory;

	/**
	 * @var \SS6\ShopBundle\Model\AdminNavigation\Breadcrumb
	 */
	private $breadcrumb;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Currency\CurrencyFacade
	 */
	private $currencyFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Transport\Detail\TransportDetailFactory
	 */
	private $transportDetailFactory;

	/**
	 * @var \SS6\ShopBundle\Model\Transport\Grid\TransportGridFactory
	 */
	private $transportGridFactory;

	/**
	 * @var \SS6\ShopBundle\Model\Transport\TransportEditDataFactory
	 */
	private $transportEditDataFactory;

	/**
	 * @var \SS6\ShopBundle\Model\Transport\TransportEditFacade
	 */
	private $transportEditFacade;

	/**
	 * @var \Symfony\Component\Translation\Translator
	 */
	private $translator;

	public function __construct(
		Translator $translator,
		EntityManager $em,
		TransportEditFacade $transportEditFacade,
		TransportGridFactory $transportGridFactory,
		TransportEditFormTypeFactory $transportEditFormTypeFactory,
		TransportEditDataFactory $transportEditDataFactory,
		CurrencyFacade $currencyFacade,
		TransportDetailFactory $transportDetailFactory,
		Breadcrumb $breadcrumb
	) {
		$this->translator = $translator;
		$this->em = $em;
		$this->transportEditFacade = $transportEditFacade;
		$this->transportGridFactory = $transportGridFactory;
		$this->transportEditFormTypeFactory = $transportEditFormTypeFactory;
		$this->transportEditDataFactory = $transportEditDataFactory;
		$this->currencyFacade = $currencyFacade;
		$this->transportDetailFactory = $transportDetailFactory;
		$this->breadcrumb = $breadcrumb;
	}

	/**
	 * @Route("/transport/new/")
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function newAction(Request $request) {
		$transportEditData = $this->transportEditDataFactory->createDefault();

		$form = $this->createForm($this->transportEditFormTypeFactory->create(), $transportEditData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$transport = $this->em->transactional(
				function () use ($form) {
					return $this->transportEditFacade->create($form->getData());
				}
			);

			$this->getFlashMessageSender()->addSuccessFlashTwig('Byla vytvořena doprava'
					. ' <strong><a href="{{ url }}">{{ name }}</a></strong>', [
				'name' => $transport->getName(),
				'url' => $this->generateUrl('admin_transport_edit', ['id' => $transport->getId()]),
			]);
			return $this->redirect($this->generateUrl('admin_transportandpayment_list'));
		}

		if ($form->isSubmitted() && !$form->isValid()) {
			$this->getFlashMessageSender()->addErrorFlashTwig('Prosím zkontrolujte si správnost vyplnění všech údajů');
		}

		return $this->render('@SS6Shop/Admin/Content/Transport/new.html.twig', [
			'form' => $form->createView(),
			'currencies' => $this->currencyFacade->getAllIndexedById(),
		]);
	}

	/**
	 * @Route("/transport/edit/{id}", requirements={"id" = "\d+"})
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param int $id
	 */
	public function editAction(Request $request, $id) {
		$transport = $this->transportEditFacade->getById($id);
		/* @var $transport \SS6\ShopBundle\Model\Transport\Transport */

		$transportEditData = $this->transportEditDataFactory->createFromTransport($transport);

		$form = $this->createForm($this->transportEditFormTypeFactory->create(), $transportEditData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$this->em->transactional(
				function () use ($transport, $transportEditData) {
					$this->transportEditFacade->edit($transport, $transportEditData);
				}
			);

			$this->getFlashMessageSender()->addSuccessFlashTwig('Byla upravena doprava'
					. ' <strong><a href="{{ url }}">{{ name }}</a></strong>', [
				'name' => $transport->getName(),
				'url' => $this->generateUrl('admin_transport_edit', ['id' => $transport->getId()]),
			]);
			return $this->redirect($this->generateUrl('admin_transportandpayment_list'));
		}

		if ($form->isSubmitted() && !$form->isValid()) {
			$this->getFlashMessageSender()->addErrorFlash('Prosím zkontrolujte si správnost vyplnění všech údajů');
		}

		$this->breadcrumb->replaceLastItem(new MenuItem($this->translator->trans('Editace dopravy - ') . $transport->getName()));

		return $this->render('@SS6Shop/Admin/Content/Transport/edit.html.twig', [
			'form' => $form->createView(),
			'transportDetail' => $this->transportDetailFactory->createDetailForTransportWithIndependentPrices($transport),
			'currencies' => $this->currencyFacade->getAllIndexedById(),
		]);
	}

	/**
	 * @Route("/transport/delete/{id}", requirements={"id" = "\d+"})
	 * @CsrfProtection
	 * @param int $id
	 */
	public function deleteAction($id) {
		try {
			$transportName = $this->transportEditFacade->getById($id)->getName();
			$this->em->transactional(
				function () use ($id) {
					$this->transportEditFacade->deleteById($id);
				}
			);

			$this->getFlashMessageSender()->addSuccessFlashTwig('Doprava <strong>{{ name }}</strong> byla smazána', [
				'name' => $transportName,
			]);
		} catch (\SS6\ShopBundle\Model\Transport\Exception\TransportNotFoundException $ex) {
			$this->getFlashMessageSender()->addErrorFlash('Zvolená doprava neexistuje.');
		}

		return $this->redirect($this->generateUrl('admin_transportandpayment_list'));
	}

	public function listAction() {
		$grid = $this->transportGridFactory->create();

		return $this->render('@SS6Shop/Admin/Content/Transport/list.html.twig', [
			'gridView' => $grid->createView(),
		]);
	}

}
