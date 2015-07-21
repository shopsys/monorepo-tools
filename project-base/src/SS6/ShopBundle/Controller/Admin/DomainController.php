<?php

namespace SS6\ShopBundle\Controller\Admin;

use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Component\Translation\Translator;
use SS6\ShopBundle\Controller\Admin\BaseController;
use SS6\ShopBundle\Form\Admin\Domain\DomainFormType;
use SS6\ShopBundle\Model\AdminNavigation\Breadcrumb;
use SS6\ShopBundle\Model\Domain\Domain;
use SS6\ShopBundle\Model\Domain\DomainFacade;
use SS6\ShopBundle\Model\Domain\SelectedDomain;
use SS6\ShopBundle\Model\Grid\ArrayDataSource;
use SS6\ShopBundle\Model\Grid\GridFactory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DomainController extends BaseController {

	/**
	 * @var \SS6\ShopBundle\Model\Domain\Domain
	 */
	private $domain;

	/**
	 * @var \SS6\ShopBundle\Model\Domain\SelectedDomain
	 */
	private $selectedDomain;

	/**
	 * @var \SS6\ShopBundle\Model\Grid\GridFactory
	 */
	private $gridFactory;

	/**
	 * @var \SS6\ShopBundle\Model\AdminNavigation\Breadcrumb
	 */
	private $breadcrumb;

	/**
	 * @var \SS6\ShopBundle\Component\Translation\Translator
	 */
	private $translator;

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Model\Domain\DomainFacade
	 */
	private $domainFacade;

	public function __construct(
		Domain $domain,
		SelectedDomain $selectedDomain,
		GridFactory $gridFactory,
		Breadcrumb $breadcrumb,
		Translator $translator,
		EntityManager $em,
		DomainFacade $domainFacade
	) {
		$this->domain = $domain;
		$this->selectedDomain = $selectedDomain;
		$this->gridFactory = $gridFactory;
		$this->breadcrumb = $breadcrumb;
		$this->translator = $translator;
		$this->em = $em;
		$this->domainFacade = $domainFacade;
	}

	public function domainTabsAction() {
		return $this->render('@SS6Shop/Admin/Inline/Domain/tabs.html.twig', [
			'domainConfigs' => $this->domain->getAll(),
			'selectedDomainId' => $this->selectedDomain->getId(),
		]);
	}

	/**
	 * @Route("/multidomain/select_domain/{id}", requirements={"id" = "\d+"})
	 * @param Request $request
	 */
	public function selectDomainAction(Request $request, $id) {
		$id = (int)$id;

		$this->selectedDomain->setId($id);

		$referer = $request->server->get('HTTP_REFERER');
		if ($referer === null) {
			return $this->redirect($this->generateUrl('admin_dashboard'));
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

		$grid->addColumn('name', 'name', 'Název domény');
		$grid->addColumn('locale', 'locale', 'Jazyk');
		$grid->addColumn('icon', 'icon', 'Ikona');

		$grid->setTheme('@SS6Shop/Admin/Content/Domain/listGrid.html.twig');

		return $this->render('@SS6Shop/Admin/Content/Domain/list.html.twig', [
			'gridView' => $grid->createView(),
		]);
	}

	/**
	 * @Route("/domain/edit/{id}", requirements={"id" = "\d+"})
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
					$this->em->transactional(
						function () use ($id, $iconName) {
							$this->domainFacade->editIcon($id, $iconName);
						}
					);
				}

				$this->getFlashMessageSender()->addSuccessFlashTwig(
					'Byla upravena doména <strong>{{ name }}</strong>', ['name' => $domain->getName()]);

				return new JsonResponse(['result' => 'valid']);
			} catch (\SS6\ShopBundle\Model\Image\Processing\Exception\FileIsNotSupportedImageException $ex) {
				$this->getFlashMessageSender()->addErrorFlash('Typ souboru není podporován.');
			} catch (\SS6\ShopBundle\Model\FileUpload\Exception\MoveToFolderFailedException $ex) {
				$this->getFlashMessageSender()->addErrorFlash('Nahrání souboru selhalo, zkuste to, prosím, znovu.');
			}

		}

		if ($form->isSubmitted() && !$form->isValid()) {
			return new JsonResponse(['result' => 'invalid', 'errors' => $this->getAllErrorsAsArray($form)]);
		}

		return $this->render('@SS6Shop/Admin/Content/Domain/edit.html.twig', [
			'form' => $form->createView(),
			'domain' => $domain,
		]);

	}

	/**
	 * @param \Symfony\Component\Form\Form $form
	 * @return string[]
	 */
	private function getAllErrorsAsArray($form) {
		$flashMessageBag = $this->get('ss6.shop.flash_message.bag.admin');
		/* @var $flashMessageBag \SS6\ShopBundle\Model\FlashMessage\Bag */
		$errors = $flashMessageBag->getErrorMessages();

		foreach ($form->getErrors(true) as $error) {
			/* @var $error \Symfony\Component\Form\FormError */
			$errors[] = $error->getMessage();
		}

		return $errors;
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
