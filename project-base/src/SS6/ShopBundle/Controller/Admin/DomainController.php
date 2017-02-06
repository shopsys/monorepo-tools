<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Component\Controller\AdminBaseController;
use SS6\ShopBundle\Component\Controller\ErrorService;
use SS6\ShopBundle\Component\Domain\Domain;
use SS6\ShopBundle\Component\Domain\DomainFacade;
use SS6\ShopBundle\Component\Domain\SelectedDomain;
use SS6\ShopBundle\Component\Grid\ArrayDataSource;
use SS6\ShopBundle\Component\Grid\GridFactory;
use SS6\ShopBundle\Form\Admin\Domain\DomainFormType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DomainController extends AdminBaseController {

	/**
	 * @var \SS6\ShopBundle\Component\Domain\Domain
	 */
	private $domain;

	/**
	 * @var \SS6\ShopBundle\Component\Domain\SelectedDomain
	 */
	private $selectedDomain;

	/**
	 * @var \SS6\ShopBundle\Component\Grid\GridFactory
	 */
	private $gridFactory;

	/**
	 * @var \SS6\ShopBundle\Component\Domain\DomainFacade
	 */
	private $domainFacade;

	/**
	 * @var \SS6\ShopBundle\Component\Controller\ErrorService
	 */
	private $errorService;

	public function __construct(
		Domain $domain,
		SelectedDomain $selectedDomain,
		GridFactory $gridFactory,
		DomainFacade $domainFacade,
		ErrorService $errorService
	) {
		$this->domain = $domain;
		$this->selectedDomain = $selectedDomain;
		$this->gridFactory = $gridFactory;
		$this->domainFacade = $domainFacade;
		$this->errorService = $errorService;
	}

	public function domainTabsAction() {
		return $this->render('@SS6Shop/Admin/Inline/Domain/tabs.html.twig', [
			'domainConfigs' => $this->domain->getAll(),
			'selectedDomainId' => $this->selectedDomain->getId(),
		]);
	}

	/**
	 * @Route("/multidomain/select-domain/{id}", requirements={"id" = "\d+"})
	 * @param Request $request
	 */
	public function selectDomainAction(Request $request, $id) {
		$id = (int)$id;

		$this->selectedDomain->setId($id);

		$referer = $request->server->get('HTTP_REFERER');
		if ($referer === null) {
			return $this->redirectToRoute('admin_dashboard');
		} else {
			return $this->redirect($referer);
		}
	}

	/**
	 * @Route("/domain/list")
	 */
	public function listAction() {
		$dataSource = new ArrayDataSource($this->loadData(), 'id');

		$grid = $this->gridFactory->create('domainsList', $dataSource);

		$grid->addColumn('name', 'name', t('Domain name'));
		$grid->addColumn('locale', 'locale', t('Language'));
		$grid->addColumn('icon', 'icon', t('Icon'));

		$grid->setTheme('@SS6Shop/Admin/Content/Domain/listGrid.html.twig');

		return $this->render('@SS6Shop/Admin/Content/Domain/list.html.twig', [
			'gridView' => $grid->createView(),
		]);
	}

	/**
	 * @Route("/domain/edit/{id}", requirements={"id" = "\d+"}, condition="request.isXmlHttpRequest()")
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param int $id
	 */
	public function editAction(Request $request, $id) {
		$id = (int)$id;
		$domain = $this->domain->getDomainConfigById($id);

		$form = $this->createForm(
			new DomainFormType(),
			null,
			[
				'method' => 'POST',
				'action' => $this->generateUrl('admin_domain_edit', ['id' => $id]),
			]
		);

		$form->handleRequest($request);

		if ($form->isValid()) {
			try {
				if (count($form->getData()[DomainFormType::DOMAIN_ICON]) !== 0) {
					$iconName = reset($form->getData()[DomainFormType::DOMAIN_ICON]);

					$this->domainFacade->editIcon($id, $iconName);
				}

				$this->getFlashMessageSender()->addSuccessFlashTwig(
					t('Domain <strong>{{ name }}</strong> modified'),
					['name' => $domain->getName()]
				);

				return new JsonResponse(['result' => 'valid']);
			} catch (\SS6\ShopBundle\Component\Image\Processing\Exception\FileIsNotSupportedImageException $ex) {
				$this->getFlashMessageSender()->addErrorFlash(t('File type not supported.'));
			} catch (\SS6\ShopBundle\Component\FileUpload\Exception\MoveToFolderFailedException $ex) {
				$this->getFlashMessageSender()->addErrorFlash(t('File upload failed, try again please.'));
			}

		}

		if ($form->isSubmitted() && !$form->isValid()) {
			$flashMessageBag = $this->get('ss6.shop.component.flash_message.bag.admin');
			return new JsonResponse([
				'result' => 'invalid',
				'errors' => $this->errorService->getAllErrorsAsArray($form, $flashMessageBag),
			]);
		}

		return $this->render('@SS6Shop/Admin/Content/Domain/edit.html.twig', [
			'form' => $form->createView(),
			'domain' => $domain,
		]);

	}

	private function loadData() {
		$data = [];
		foreach ($this->domain->getAll() as $domainConfig) {
			$data[] = [
				'id' => $domainConfig->getId(),
				'name' => $domainConfig->getName(),
				'locale' => $domainConfig->getLocale(),
				'icon' => null,
			];
		}

		return $data;
	}

}
