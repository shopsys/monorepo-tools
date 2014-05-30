<?php

namespace SS6\ShopBundle\Controller\Admin;

use APY\DataGridBundle\Grid\Action\RowAction;
use APY\DataGridBundle\Grid\Column\BooleanColumn;
use APY\DataGridBundle\Grid\Row;
use APY\DataGridBundle\Grid\Source\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Form\Admin\Product\ProductFormType;
use SS6\ShopBundle\Model\PKGrid\PKGrid;
use SS6\ShopBundle\Model\Product\Product;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends Controller {
	
	/**
	 * @Route("/product/edit/{id}", requirements={"id" = "\d+"})
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param int $id
	 */
	public function editAction(Request $request, $id) {
		$flashMessage = $this->get('ss6.shop.flash_message.admin');
		/* @var $flashMessage \SS6\ShopBundle\Model\FlashMessage\FlashMessage */
		$fileUpload = $this->get('ss6.shop.file_upload');
		/* @var $fileUpload \SS6\ShopBundle\Model\FileUpload\FileUpload */
		$productRepository = $this->get('ss6.shop.product.product_repository');
		/* @var $productRepository \SS6\ShopBundle\Model\Product\ProductRepository */

		$product = $productRepository->getById($id);
		$form = $this->createForm(new ProductFormType($fileUpload));
		$productData = array();

		if (!$form->isSubmitted()) {
			$productData['id'] = $product->getId();
			$productData['name'] = $product->getName();
			$productData['catnum'] = $product->getCatnum();
			$productData['partno'] = $product->getPartno();
			$productData['ean'] = $product->getEan();
			$productData['description'] = $product->getDescription();
			$productData['price'] = $product->getPrice();
			$productData['sellingFrom'] = $product->getSellingFrom();
			$productData['sellingTo'] = $product->getSellingTo();
			$productData['stockQuantity'] = $product->getStockQuantity();
			$productData['hidden'] = $product->isHidden();
		}

		$form->setData($productData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$productEditFacade = $this->get('ss6.shop.product.product_edit_facade');
			/* @var $productEditFacade \SS6\ShopBundle\Model\Product\ProductEditFacade */
			$product = $productEditFacade->edit($id, $form->getData());

			$flashMessage->addSuccess('Bylo upraveno zboží ' . $product->getName());
			return $this->redirect($this->generateUrl('admin_product_edit', array('id' => $product->getId())));
		}

		if ($form->isSubmitted() && !$form->isValid()) {
			$flashMessage->addError('Prosím zkontrolujte si správnost vyplnění všech údajů');
		}
		
		return $this->render('@SS6Shop/Admin/Content/Product/edit.html.twig', array(
			'form' => $form->createView(),
			'product' => $product,
		));
	}
	
	/**
	 * @Route("/product/new/")
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function newAction(Request $request) {
		$flashMessage = $this->get('ss6.shop.flash_message.admin');
		/* @var $flashMessage \SS6\ShopBundle\Model\FlashMessage\FlashMessage */
		$fileUpload = $this->get('ss6.shop.file_upload');
		/* @var $fileUpload \SS6\ShopBundle\Model\FileUpload\FileUpload */

		$form = $this->createForm(new ProductFormType($fileUpload));
		
		$productData = array();

		if (!$form->isSubmitted()) {
			$productData['hidden'] = false;
		}

		$form->setData($productData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$productEditFacade = $this->get('ss6.shop.product.product_edit_facade');
			/* @var $productEditFacade \SS6\ShopBundle\Model\Product\ProductEditFacade */
			$product = $productEditFacade->create($form->getData());

			$flashMessage->addSuccess('Bylo vytvořeno zboží ' . $product->getName());
			return $this->redirect($this->generateUrl('admin_product_list'));
		}

		if ($form->isSubmitted() && !$form->isValid()) {
			$flashMessage->addError('Prosím zkontrolujte si správnost vyplnění všech údajů');
		}
		
		return $this->render('@SS6Shop/Admin/Content/Product/new.html.twig', array(
			'form' => $form->createView(),
		));
	}

	/**
	 * @Route("/product/list/")
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function listAction(Request $request) {
		$queryBuilder = $this->getDoctrine()->getManager()->createQueryBuilder();
		$queryBuilder
			->select('p')
			->from(Product::class, 'p');

		$PKGrid = new PKGrid($this->container, 'productList');
		$PKGrid->allowPaging();
		$PKGrid->setQueryBuilder($queryBuilder, 'p.id');

		$PKGrid->addColumn('visible', 'p.visible', 'Viditelnost', true);
		$PKGrid->addColumn('name', 'p.name', 'Název', true);
		$PKGrid->addColumn('price', 'p.price', 'Cena', true)->setClass('text-right');

		$PKGrid->addActionColumn('edit', 'Upravit', 'admin_product_edit', array('id' =>'p.id'));
		$PKGrid->addActionColumn('delete', 'Smazat', 'admin_product_delete', array('id' =>'p.id'))
			->setConfirmMessage('Opravdu chcete odstranit toto zboží?');

		return $PKGrid->getGridResponse('@SS6Shop/Admin/Content/Product/list.html.twig');

		$administrator = $this->getUser();
		/* @var $administrator \SS6\ShopBundle\Model\Administrator\Administrator */
		$grid = $this->get('grid');
		/* @var $grid \APY\DataGridBundle\Grid\Grid */

		$source = new Entity(Product::class);		
		$grid->setSource($source);
		$grid->setId('productList');
		
		$customLimit = $administrator->getLimitByGridId($grid->getId());
		if ($customLimit !== null) {
			$grid->setDefaultLimit($customLimit);
		}
		
		$grid->getColumns()->addColumn(new BooleanColumn(array(
			'id' => 'visible',
			'filterable' => false,
			'sortable' => false,
		)));
		
		$grid->setVisibleColumns(array('visible', 'name', 'price'));
		$grid->setColumnsOrder(array('visible', 'name', 'price'));
		$grid->getColumns()->getColumnById('visible')->setTitle('Viditelné')->setClass('table-col-10');
		$grid->getColumns()->getColumnById('name')->setTitle('Název')->setClass('table-col-60');
		$grid->getColumns()->getColumnById('price')->setTitle('Cena')->setClass('table-col-15');
		
		$grid->hideFilters();
		$grid->setActionsColumnTitle('Akce');
		$grid->setDefaultOrder('name', 'asc');
		
		$detailRowAction = new RowAction('Upravit', 'admin_product_edit');
		$detailRowAction->setRouteParameters(array('id'));
		$detailRowAction->setAttributes(array('type' => 'edit'));
		$grid->addRowAction($detailRowAction);
		
		$deleteRowAction = new RowAction('Smazat', 'admin_product_delete', true);
		$deleteRowAction->setConfirmMessage('Opravdu si přejete zboží smazat?');
		$deleteRowAction->setAttributes(array('type' => 'delete'));
		$deleteRowAction->setRouteParameters(array('id'));
		$grid->addRowAction($deleteRowAction);
		
		$repository = $this->getDoctrine()->getRepository(Product::class);
		$source->manipulateRow(function (Row $row) use ($repository) {
			$product = $repository->find($row->getField('id'));
			$row->setField('visible', $product->isVisible());
			
			return $row;
		});

		if ($grid->isReadyForRedirect()) {
			$gridData = $request->get($grid->getHash());
			if (!empty($gridData[\APY\DataGridBundle\Grid\Grid::REQUEST_QUERY_LIMIT])) {
				$administratorGridFacade = $this->get('ss6.shop.administrator.administrator_grid_facade');
				/* @var $administratorGridFacade \SS6\ShopBundle\Model\Administrator\AdministratorGridFacade */
				$gridLimit = $gridData[\APY\DataGridBundle\Grid\Grid::REQUEST_QUERY_LIMIT];
				if ($gridLimit !== null) {
					$administratorGridFacade->saveGridLimit($administrator, $grid->getId(), (int)$gridLimit);
				}
			}
		}
		
		return $grid->getGridResponse('@SS6Shop/Admin/Content/Product/list.html.twig');
	}
	
	/**
	 * @Route("/product/delete/{id}", requirements={"id" = "\d+"})
	 * @param int $id
	 */
	public function deleteAction($id) {
		$flashMessage = $this->get('ss6.shop.flash_message.admin');
		/* @var $flashMessage \SS6\ShopBundle\Model\FlashMessage\FlashMessage */

		$productRepository = $this->get('ss6.shop.product.product_repository');
		/* @var $productRepository \SS6\ShopBundle\Model\Product\ProductRepository */

		$productName = $productRepository->getById($id)->getName();
		$this->get('ss6.shop.product.product_edit_facade')->delete($id);
		$flashMessage->addSuccess('Produkt ' . $productName . ' byl smazán');
		
		return $this->redirect($this->generateUrl('admin_product_list'));
	}
}
