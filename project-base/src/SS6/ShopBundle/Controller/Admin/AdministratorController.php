<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Component\Controller\AdminBaseController;
use SS6\ShopBundle\Component\Grid\GridFactory;
use SS6\ShopBundle\Component\Grid\QueryBuilderDataSource;
use SS6\ShopBundle\Component\Router\Security\Annotation\CsrfProtection;
use SS6\ShopBundle\Form\Admin\Administrator\AdministratorFormType;
use SS6\ShopBundle\Model\Administrator\Activity\AdministratorActivityFacade;
use SS6\ShopBundle\Model\Administrator\AdministratorData;
use SS6\ShopBundle\Model\Administrator\AdministratorFacade;
use SS6\ShopBundle\Model\AdminNavigation\Breadcrumb;
use SS6\ShopBundle\Model\AdminNavigation\MenuItem;
use Symfony\Component\HttpFoundation\Request;

class AdministratorController extends AdminBaseController {

	const MAX_ADMINISTRATOR_ACTIVITIES_COUNT = 10;

	/**
	 * @var \SS6\ShopBundle\Model\AdminNavigation\Breadcrumb
	 */
	private $breadcrumb;

	/**
	 * @var \SS6\ShopBundle\Model\Administrator\AdministratorFacade
	 */
	private $administratorFacade;

	/**
	 * @var \SS6\ShopBundle\Component\Grid\GridFactory
	 */
	private $gridFactory;

	/**
	 * @var \SS6\ShopBundle\Model\Administrator\Activity\AdministratorActivityFacade
	 */
	private $administratorActivityFacade;

	public function __construct(
		AdministratorFacade $administratorFacade,
		GridFactory $gridFactory,
		Breadcrumb $breadcrumb,
		AdministratorActivityFacade $administratorActivityFacade
	) {
		$this->administratorFacade = $administratorFacade;
		$this->gridFactory = $gridFactory;
		$this->breadcrumb = $breadcrumb;
		$this->administratorActivityFacade = $administratorActivityFacade;
	}

	/**
	 * @Route("/administrator/list/")
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function listAction() {
		$queryBuilder = $this->administratorFacade->getAllListableQueryBuilder();
		$dataSource = new QueryBuilderDataSource($queryBuilder, 'a.id');

		$grid = $this->gridFactory->create('administratorList', $dataSource);
		$grid->setDefaultOrder('realName');

		$grid->addColumn('realName', 'a.realName', t('Celé jméno'), true);
		$grid->addColumn('email', 'a.email', t('E-mail'));

		$grid->setActionColumnClassAttribute('table-col table-col-10');
		$grid->addEditActionColumn('admin_administrator_edit', ['id' => 'a.id']);
		$grid->addDeleteActionColumn('admin_administrator_delete', ['id' => 'a.id'])
			->setConfirmMessage('Opravdu chcete odstranit tohoto administrátora?');

		$grid->setTheme('@SS6Shop/Admin/Content/Administrator/listGrid.html.twig');

		return $this->render('@SS6Shop/Admin/Content/Administrator/list.html.twig', [
			'gridView' => $grid->createView(),
		]);
	}

	/**
	 * @Route("/administrator/edit/{id}", requirements={"id" = "\d+"})
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param int $id
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	public function editAction(Request $request, $id) {
		$administrator = $this->administratorFacade->getById($id);
		if ($administrator->isSuperadmin()) {
			$message = 'Superadmin cannot be edited.';
			throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException($message);
		}

		$form = $this->createForm(new AdministratorFormType(AdministratorFormType::SCENARIO_EDIT));

		$administratorData = new AdministratorData();
		$administratorData->setFromEntity($administrator);

		$form->setData($administratorData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			try {
				$this->administratorFacade->edit($id, $administratorData);

				$this->getFlashMessageSender()->addSuccessFlashTwig(
					t('Byl upraven administrátor <strong><a href="{{ url }}">{{ name }}</a></strong>'),
					[
						'name' => $administratorData->realName,
						'url' => $this->generateUrl('admin_administrator_edit', ['id' => $administrator->getId()]),
					]
				);
				return $this->redirectToRoute('admin_administrator_list');

			} catch (\SS6\ShopBundle\Model\Administrator\Exception\DuplicateSuperadminNameException $ex) {
				$this->getFlashMessageSender()->addErrorFlashTwig(
					t('Omlouváme se, ale jméno <strong>{{ name }}</strong> je vyhrazeno pro systémovou funkci. Použijte prosím jiné'),
					[
						'name' => $administratorData->username,
					]
				);
			} catch (\SS6\ShopBundle\Model\Administrator\Exception\DuplicateUserNameException $ex) {
				$this->getFlashMessageSender()->addErrorFlashTwig(
					t('Administrátor s přihlašovacím jménem <strong>{{ name }}</strong> již existuje'),
					[
						'name' => $administratorData->username,
					]
				);
			}

		}

		if ($form->isSubmitted() && !$form->isValid()) {
			$this->getFlashMessageSender()->addErrorFlash(t('Prosím zkontrolujte si správnost vyplnění všech údajů'));
		}

		$this->breadcrumb->overrideLastItem(
			new MenuItem(t('Editace administrátora - %name%', ['%name%' => $administrator->getRealName()]))
		);

		$lastAdminActivities = $this->administratorActivityFacade->getLastAdministratorActivities(
			$administrator,
			self::MAX_ADMINISTRATOR_ACTIVITIES_COUNT
		);

		return $this->render('@SS6Shop/Admin/Content/Administrator/edit.html.twig', [
			'form' => $form->createView(),
			'administrator' => $administrator,
			'lastAdminActivities' => $lastAdminActivities,
		]);
	}

	/**
	 * @Route("/administrator/new/")
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function newAction(Request $request) {
		$form = $this->createForm(
			new AdministratorFormType(AdministratorFormType::SCENARIO_CREATE),
			null,
			['validation_groups' => ['Default', AdministratorFormType::SCENARIO_CREATE]]
		);

		$administratorData = new AdministratorData();

		$form->setData($administratorData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$administratorData = $form->getData();

			try {
				$administrator = $this->administratorFacade->create($administratorData);

				$this->getFlashMessageSender()->addSuccessFlashTwig(
					t('Byl vytvořen administrátor <strong><a href="{{ url }}">{{ name }}</a></strong>'),
					[
						'name' => $administrator->getRealName(),
						'url' => $this->generateUrl('admin_administrator_edit', ['id' => $administrator->getId()]),
					]
				);
				return $this->redirectToRoute('admin_administrator_list');

			} catch (\SS6\ShopBundle\Model\Administrator\Exception\DuplicateSuperadminNameException $ex) {
				$this->getFlashMessageSender()->addErrorFlashTwig(
					t('Omlouváme se, ale jméno <strong>{{ name }}</strong> je vyhrazeno pro systémovou funkci. Použijte prosím jiné'),
					[
						'name' => $administratorData->username,
					]
				);
			} catch (\SS6\ShopBundle\Model\Administrator\Exception\DuplicateUserNameException $ex) {
				$this->getFlashMessageSender()->addErrorFlashTwig(
					t('Administrátor s přihlašovacím jménem <strong>{{ name }}</strong> již existuje'),
					[
						'name' => $administratorData->username,
					]
				);
			}

		}

		if ($form->isSubmitted() && !$form->isValid()) {
			$this->getFlashMessageSender()->addErrorFlash(t('Prosím zkontrolujte si správnost vyplnění všech údajů'));
		}

		return $this->render('@SS6Shop/Admin/Content/Administrator/new.html.twig', [
			'form' => $form->createView(),
		]);
	}

	/**
	 * @Route("/administrator/delete/{id}", requirements={"id" = "\d+"})
	 * @CsrfProtection
	 * @param int $id
	 */
	public function deleteAction($id) {
		try {
			$realName = $this->administratorFacade->getById($id)->getRealName();

			$this->administratorFacade->delete($id);
			$this->getFlashMessageSender()->addSuccessFlashTwig(
				t('Administrátor <strong>{{ name }}</strong> byl smazán.'),
				[
					'name' => $realName,
				]
			);
		} catch (\SS6\ShopBundle\Model\Administrator\Exception\DeletingSelfException $ex) {
			$this->getFlashMessageSender()->addErrorFlash(t('Nemůžete smazat sami sebe.'));
		} catch (\SS6\ShopBundle\Model\Administrator\Exception\DeletingLastAdministratorException $ex) {
			$this->getFlashMessageSender()->addErrorFlashTwig(
				t('Administrátor <strong>{{ name }}</strong> je jediný a nemůže být smazán.'),
				[
					'name' => $this->administratorFacade->getById($id)->getRealName(),
				]
			);
		} catch (\SS6\ShopBundle\Model\Administrator\Exception\AdministratorNotFoundException $ex) {
			$this->getFlashMessageSender()->addErrorFlash(t('Zvolený administrátor neexistuje.'));
		}

		return $this->redirectToRoute('admin_administrator_list');
	}
}
