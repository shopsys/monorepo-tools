<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Component\Translation\Translator;
use SS6\ShopBundle\Controller\Admin\BaseController;
use SS6\ShopBundle\Form\Admin\Advert\AdvertFormTypeFactory;
use SS6\ShopBundle\Model\Administrator\AdministratorGridFacade;
use SS6\ShopBundle\Model\AdminNavigation\Breadcrumb;
use SS6\ShopBundle\Model\AdminNavigation\MenuItem;
use SS6\ShopBundle\Model\Advert\Advert;
use SS6\ShopBundle\Model\Advert\AdvertData;
use SS6\ShopBundle\Model\Advert\AdvertEditFacade;
use SS6\ShopBundle\Model\Advert\AdvertPositionList;
use SS6\ShopBundle\Model\Domain\SelectedDomain;
use SS6\ShopBundle\Model\Grid\GridFactory;
use SS6\ShopBundle\Model\Grid\QueryBuilderWithRowManipulatorDataSource;
use Symfony\Component\HttpFoundation\Request;

class AdvertController extends BaseController {

	/**
	 * @var \SS6\ShopBundle\Model\Advert\AdvertEditFacade
	 */
	private $advertEditFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Administrator\AdministratorGridFacade
	 */
	private $administratorGridFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Grid\GridFactory
	 */
	private $gridFactory;

	/**
	 * @var \SS6\ShopBundle\Model\Domain\SelectedDomain
	 */
	private $selectedDomain;

	/**
	 * @var \SS6\ShopBundle\Model\AdminNavigation\Breadcrumb
	 */
	private $breadcrumb;

	/**
	 * @var \SS6\ShopBundle\Model\Advert\AdvertFormTypeFactory
	 */
	private $advertFormTypeFactory;

	/**
	 * @var \SS6\ShopBundle\Component\Translation\Translator
	 */
	private $translator;

	/**
	 * @var \SS6\ShopBundle\Model\Advert\AdvertPositionList
	 */
	private $advertPositionList;

	public function __construct(
		AdvertEditFacade $advertEditFacade,
		AdministratorGridFacade $administratorGridFacade,
		GridFactory $gridFactory,
		SelectedDomain $selectedDomain,
		Breadcrumb $breadcrumb,
		AdvertFormTypeFactory $advertFormTypeFactory,
		Translator $translator,
		AdvertPositionList $advertPositionList
	) {
		$this->advertEditFacade = $advertEditFacade;
		$this->administratorGridFacade = $administratorGridFacade;
		$this->gridFactory = $gridFactory;
		$this->selectedDomain = $selectedDomain;
		$this->breadcrumb = $breadcrumb;
		$this->advertFormTypeFactory = $advertFormTypeFactory;
		$this->translator = $translator;
		$this->advertPositionList = $advertPositionList;
	}

	/**
	 * @Route("/advert/edit/{id}", requirements={"id" = "\d+"})
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param int $id
	 */
	public function editAction(Request $request, $id) {
		$advert = $this->advertEditFacade->getById($id);
		$form = $this->createForm($this->advertFormTypeFactory->create($advert));

		$advertData = new AdvertData();

		if (!$form->isSubmitted()) {
			$advertData->setFromEntity($advert);
		}

		$form->setData($advertData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$this->advertEditFacade->edit($id, $advertData);

			$this->getFlashMessageSender()
				->addSuccessFlashTwig('Reklama <strong>{{ name }}</strong> byla upravena', [
					'name' => $advert->getName()
				]);
			return $this->redirect($this->generateUrl('admin_advert_list'));
		}

		if ($form->isSubmitted() && !$form->isValid()) {
			$this->getFlashMessageSender()->addErrorFlashTwig('Prosím zkontrolujte si správnost vyplnění všech údajů');
		}

		$this->breadcrumb->replaceLastItem(new MenuItem($this->translator->trans('Editace reklamy - ') . $advert->getName()));

		return $this->render('@SS6Shop/Admin/Content/Advert/edit.html.twig', [
			'form' => $form->createView(),
			'advert' => $advert,
		]);
	}

	/**
	 * @Route("/advert/list/")
	 */
	public function listAction() {
		$administrator = $this->getUser();
		/* @var $administrator \SS6\ShopBundle\Model\Administrator\Administrator */

		$queryBuilder = $this->getDoctrine()->getManager()->createQueryBuilder();
		$queryBuilder
			->select('a')
			->from(Advert::class, 'a')
			->where('a.domainId = :selectedDomainId')
			->setParameter('selectedDomainId', $this->selectedDomain->getId());
		$dataSource = new QueryBuilderWithRowManipulatorDataSource(
			$queryBuilder,
			'a.id',
			function ($row) {
				$advert = $this->advertEditFacade->getById($row['a']['id']);
				$row['advert'] = $advert;
				return $row;
			}
		);

		$grid = $this->gridFactory->create('advertList', $dataSource);
		$grid->allowPaging();
		$grid->setDefaultOrder('name');

		$grid->addColumn('visible', 'a.hidden', 'Viditelnost', true)->setClassAttribute('table-col table-col-10');
		$grid->addColumn('name', 'a.name', 'Název', true);
		$grid->addColumn('preview', 'a.id', 'Náhled', false);
		$grid->addColumn('positionName', 'a.positionName', 'Plocha', true);

		$grid->setActionColumnClassAttribute('table-col table-col-10');
		$grid->addActionColumn('edit', 'Upravit', 'admin_advert_edit', ['id' => 'a.id']);
		$grid->addActionColumn('delete', 'Smazat', 'admin_advert_delete', ['id' => 'a.id'])
			->setConfirmMessage('Opravdu chcete odstranit tuto reklamu?');

		$grid->setTheme('@SS6Shop/Admin/Content/Advert/listGrid.html.twig', [
			'advertPositionsByName' => $this->advertPositionList->getTranslationsIndexedByValue(),
		]);

		$this->administratorGridFacade->restoreAndRememberGridLimit($administrator, $grid);

		return $this->render('@SS6Shop/Admin/Content/Advert/list.html.twig', [
			'gridView' => $grid->createView(),
		]);
	}

	/**
	 * @Route("/advert/new/")
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function newAction(Request $request) {
		$form = $this->createForm($this->advertFormTypeFactory->create());

		$advertData = new AdvertData();
		$advertData->domainId = $this->selectedDomain->getId();

		$form->setData($advertData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$advertData = $form->getData();

			$advert = $this->advertEditFacade->create($advertData);

			$this->getFlashMessageSender()
				->addSuccessFlashTwig('Reklama <strong>{{ name }}</strong> byla vytvořena', [
					'name' => $advert->getName(),
				]);
			return $this->redirect($this->generateUrl('admin_advert_list'));
		}

		if ($form->isSubmitted() && !$form->isValid()) {
			$this->getFlashMessageSender()->addErrorFlashTwig('Prosím zkontrolujte si správnost vyplnění všech údajů');
		}

		return $this->render('@SS6Shop/Admin/Content/Advert/new.html.twig', [
			'form' => $form->createView(),
		]);
	}

	/**
	 * @Route("/advert/delete/{id}", requirements={"id" = "\d+"})
	 * @param int $id
	 */
	public function deleteAction($id) {
		try {
			$fullName = $this->advertEditFacade->getById($id)->getName();
			$this->advertEditFacade->delete($id);

			$this->getFlashMessageSender()->addSuccessFlashTwig('Reklama <strong>{{ name }}</strong> byla smazána', [
				'name' => $fullName,
			]);
		} catch (\SS6\ShopBundle\Model\Advert\Exception\AdvertNotFoundException $ex) {
			$this->getFlashMessageSender()->addErrorFlash('Zvolená reklama neexistuje.');
		}

		return $this->redirect($this->generateUrl('admin_advert_list'));
	}

}
