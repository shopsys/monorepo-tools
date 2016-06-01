<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Component\Controller\AdminBaseController;
use SS6\ShopBundle\Component\Grid\GridFactory;
use SS6\ShopBundle\Component\Grid\QueryBuilderDataSource;
use SS6\ShopBundle\Component\Router\Security\Annotation\CsrfProtection;
use SS6\ShopBundle\Form\Admin\Product\Brand\BrandFormType;
use SS6\ShopBundle\Model\Administrator\AdministratorGridFacade;
use SS6\ShopBundle\Model\AdminNavigation\Breadcrumb;
use SS6\ShopBundle\Model\AdminNavigation\MenuItem;
use SS6\ShopBundle\Model\Product\Brand\Brand;
use SS6\ShopBundle\Model\Product\Brand\BrandData;
use SS6\ShopBundle\Model\Product\Brand\BrandFacade;
use Symfony\Component\HttpFoundation\Request;

class BrandController extends AdminBaseController {

	/**
	 * @var \SS6\ShopBundle\Model\AdminNavigation\Breadcrumb
	 */
	private $breadcrumb;

	/**
	 * @var \SS6\ShopBundle\Model\Administrator\AdministratorGridFacade
	 */
	private $administratorGridFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Brand\BrandFacade
	 */
	private $brandFacade;

	/**
	 * @var \SS6\ShopBundle\Component\Grid\GridFactory
	 */
	private $gridFactory;

	public function __construct(
		BrandFacade $brandFacade,
		AdministratorGridFacade $administratorGridFacade,
		GridFactory $gridFactory,
		Breadcrumb $breadcrumb
	) {
		$this->brandFacade = $brandFacade;
		$this->administratorGridFacade = $administratorGridFacade;
		$this->gridFactory = $gridFactory;
		$this->breadcrumb = $breadcrumb;
	}

	/**
	 * @Route("/brand/edit/{id}", requirements={"id" = "\d+"})
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param int $id
	 */
	public function editAction(Request $request, $id) {
		$brand = $this->brandFacade->getById($id);
		$form = $this->createForm(new BrandFormType());

		$brandData = new BrandData();
		$brandData->setFromEntity($brand);

		$form->setData($brandData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$this->brandFacade->edit($id, $brandData);

			$this->getFlashMessageSender()
				->addSuccessFlashTwig(
					t('Byla upravena značka <strong><a href="{{ url }}">{{ name }}</a></strong>'),
					[
						'name' => $brand->getName(),
						'url' => $this->generateUrl('admin_brand_edit', ['id' => $brand->getId()]),
					]
				);
			return $this->redirectToRoute('admin_brand_list');
		}

		if ($form->isSubmitted() && !$form->isValid()) {
			$this->getFlashMessageSender()->addErrorFlashTwig(t('Prosím zkontrolujte si správnost vyplnění všech údajů'));
		}

		$this->breadcrumb->overrideLastItem(new MenuItem(t('Editace značky - %name%', ['%name%' => $brand->getName()])));

		return $this->render('@SS6Shop/Admin/Content/Brand/edit.html.twig', [
			'form' => $form->createView(),
			'brand' => $brand,
		]);
	}

	/**
	 * @Route("/brand/list/")
	 */
	public function listAction() {
		$administrator = $this->getUser();
		/* @var $administrator \SS6\ShopBundle\Model\Administrator\Administrator */

		$queryBuilder = $this->getDoctrine()->getManager()->createQueryBuilder();
		$queryBuilder->select('b')->from(Brand::class, 'b');
		$dataSource = new QueryBuilderDataSource($queryBuilder, 'b.id');

		$grid = $this->gridFactory->create('brandList', $dataSource);
		$grid->enablePaging();
		$grid->setDefaultOrder('name');

		$grid->addColumn('name', 'b.name', t('Název'), true);

		$grid->setActionColumnClassAttribute('table-col table-col-10');
		$grid->addEditActionColumn('admin_brand_edit', ['id' => 'b.id']);
		$grid->addDeleteActionColumn('admin_brand_delete', ['id' => 'b.id'])
			->setConfirmMessage('Opravdu chcete odstranit tuto značku? Pokud je někde použita, bude odnastavena.');

		$grid->setTheme('@SS6Shop/Admin/Content/Brand/listGrid.html.twig');

		$this->administratorGridFacade->restoreAndRememberGridLimit($administrator, $grid);

		return $this->render('@SS6Shop/Admin/Content/Brand/list.html.twig', [
			'gridView' => $grid->createView(),
		]);
	}

	/**
	 * @Route("/brand/new/")
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function newAction(Request $request) {
		$form = $this->createForm(new BrandFormType());

		$brandData = new BrandData();

		$form->setData($brandData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$brandData = $form->getData();

			$brand = $this->brandFacade->create($brandData);

			$this->getFlashMessageSender()
				->addSuccessFlashTwig(
					t('Byla vytvořena značka <strong><a href="{{ url }}">{{ name }}</a></strong>'),
					[
						'name' => $brand->getName(),
						'url' => $this->generateUrl('admin_brand_edit', ['id' => $brand->getId()]),
					]
				);
			return $this->redirectToRoute('admin_brand_list');
		}

		if ($form->isSubmitted() && !$form->isValid()) {
			$this->getFlashMessageSender()->addErrorFlashTwig(t('Prosím zkontrolujte si správnost vyplnění všech údajů'));
		}

		return $this->render('@SS6Shop/Admin/Content/Brand/new.html.twig', [
			'form' => $form->createView(),
		]);
	}

	/**
	 * @Route("/brand/delete/{id}", requirements={"id" = "\d+"})
	 * @CsrfProtection
	 * @param int $id
	 */
	public function deleteAction($id) {
		try {
			$fullName = $this->brandFacade->getById($id)->getName();

			$this->brandFacade->deleteById($id);

			$this->getFlashMessageSender()->addSuccessFlashTwig(
				t('Značka <strong>{{ name }}</strong> byl smazána'),
				[
					'name' => $fullName,
				]
			);
		} catch (\SS6\ShopBundle\Model\Product\Brand\Exception\BrandNotFoundException $ex) {
			$this->getFlashMessageSender()->addErrorFlash(t('Zvolená značka neexistuje.'));
		}

		return $this->redirectToRoute('admin_brand_list');
	}

}
