<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Form\Admin\Product\ProductFormType;
use SS6\ShopBundle\Model\AdminNavigation\MenuItem;
use SS6\ShopBundle\Model\PKGrid\PKGrid;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\ProductData;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends Controller {
	
	/**
	 * @Route("/product/edit/{id}", requirements={"id" = "\d+"})
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param int $id
	 */
	public function editAction(Request $request, $id) {
		$flashMessageTwig = $this->get('ss6.shop.flash_message.twig_sender.admin');
		/* @var $flashMessageTwig \SS6\ShopBundle\Model\FlashMessage\TwigSender */
		$fileUpload = $this->get('ss6.shop.file_upload');
		/* @var $fileUpload \SS6\ShopBundle\Model\FileUpload\FileUpload */
		$productRepository = $this->get('ss6.shop.product.product_repository');
		/* @var $productRepository \SS6\ShopBundle\Model\Product\ProductRepository */
		$vatRepository = $this->get('ss6.shop.pricing.vat_repository');
		/* @var $fileUpload \SS6\ShopBundle\Model\Pricing\VatRepository */
		$priceCalculation = $this->get('ss6.shop.product.price_calculation');
		/* @var $priceCalculation \SS6\ShopBundle\Model\Product\PriceCalculation */

		$product = $productRepository->getById($id);

		$vats = $vatRepository->findAll();
		$form = $this->createForm(new ProductFormType($fileUpload, $vats));
		$productData = new ProductData();

		if (!$form->isSubmitted()) {
			$productData->setFromEntity($product);
		}

		$form->setData($productData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$productEditFacade = $this->get('ss6.shop.product.product_edit_facade');
			/* @var $productEditFacade \SS6\ShopBundle\Model\Product\ProductEditFacade */
			$product = $productEditFacade->edit($id, $form->getData());

			$flashMessageTwig->addSuccess('Bylo upraveno zboží <strong>{{ name }}</strong>', array(
				'name' => $product->getName(),
			));
			return $this->redirect($this->generateUrl('admin_product_edit', array('id' => $product->getId())));
		}

		if ($form->isSubmitted() && !$form->isValid()) {
			$flashMessageTwig->addError('Prosím zkontrolujte si správnost vyplnění všech údajů');
		}

		$breadcrumb = $this->get('ss6.shop.admin_navigation.breadcrumb');
		/* @var $breadcrumb \SS6\ShopBundle\Model\AdminNavigation\Breadcrumb */
		$breadcrumb->replaceLastItem(new MenuItem('Editace zboží - ' . $product->getName()));
		
		return $this->render('@SS6Shop/Admin/Content/Product/edit.html.twig', array(
			'form' => $form->createView(),
			'product' => $product,
			'price' => $priceCalculation->calculatePrice($product),
		));
	}
	
	/**
	 * @Route("/product/new/")
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function newAction(Request $request) {
		$flashMessageTwig = $this->get('ss6.shop.flash_message.twig_sender.admin');
		/* @var $flashMessageTwig \SS6\ShopBundle\Model\FlashMessage\TwigSender */
		$fileUpload = $this->get('ss6.shop.file_upload');
		/* @var $fileUpload \SS6\ShopBundle\Model\FileUpload\FileUpload */
		$vatRepository = $this->get('ss6.shop.pricing.vat_repository');
		/* @var $fileUpload \SS6\ShopBundle\Model\Pricing\VatRepository */

		$vats = $vatRepository->findAll();

		$form = $this->createForm(new ProductFormType($fileUpload, $vats));

		$productData = new ProductData();

		$form->setData($productData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$productEditFacade = $this->get('ss6.shop.product.product_edit_facade');
			/* @var $productEditFacade \SS6\ShopBundle\Model\Product\ProductEditFacade */
			$product = $productEditFacade->create($form->getData());

			$flashMessageTwig->addSuccess('Bylo vytvořeno zboží'
					. ' <strong><a href="{{ url }}">{{ name }}</a></strong>', array(
				'name' => $product->getName(),
				'url' => $this->generateUrl('admin_product_edit', array('id' => $product->getId())),
			));
			return $this->redirect($this->generateUrl('admin_product_list'));
		}

		if ($form->isSubmitted() && !$form->isValid()) {
			$flashMessageTwig->addError('Prosím zkontrolujte si správnost vyplnění všech údajů');
		}

		return $this->render('@SS6Shop/Admin/Content/Product/new.html.twig', array(
			'form' => $form->createView(),
		));
	}

	/**
	 * @Route("/product/list/")
	 */
	public function listAction() {
		$administratorGridFacade = $this->get('ss6.shop.administrator.administrator_grid_facade');
		/* @var $administratorGridFacade \SS6\ShopBundle\Model\Administrator\AdministratorGridFacade */
		$administrator = $this->getUser();
		/* @var $administrator \SS6\ShopBundle\Model\Administrator\Administrator */
		
		$queryBuilder = $this->getDoctrine()->getManager()->createQueryBuilder();
		$queryBuilder
			->select('p')
			->from(Product::class, 'p');

		$grid = new PKGrid(
			'productList',
			$this->get('request_stack'),
			$this->get('router'),
			$this->get('twig')
		);
		$grid->allowPaging();
		$grid->setDefaultOrder('name');
		$grid->setQueryBuilder($queryBuilder);

		$grid->addColumn('visible', 'p.visible', 'Viditelnost', true)->setClass('table-col table-col-10');
		$grid->addColumn('name', 'p.name', 'Název', true);
		$grid->addColumn('price', 'p.price', 'Cena', true)->setClass('text-right');

		$grid->setActionColumnClass('table-col table-col-10');
		$grid->addActionColumn('edit', 'Upravit', 'admin_product_edit', array('id' => 'p.id'));
		$grid->addActionColumn('delete', 'Smazat', 'admin_product_delete', array('id' => 'p.id'))
			->setConfirmMessage('Opravdu chcete odstranit toto zboží?');

		$administratorGridFacade->restoreAndRememberGridLimit($administrator, $grid);

		return $this->render('@SS6Shop/Admin/Content/Product/list.html.twig', array(
			'gridView' => $grid->createView(),
		));
	}
	
	/**
	 * @Route("/product/delete/{id}", requirements={"id" = "\d+"})
	 * @param int $id
	 */
	public function deleteAction($id) {
		$flashMessageTwig = $this->get('ss6.shop.flash_message.twig_sender.admin');
		/* @var $flashMessageTwig \SS6\ShopBundle\Model\FlashMessage\TwigSender */
		$productRepository = $this->get('ss6.shop.product.product_repository');
		/* @var $productRepository \SS6\ShopBundle\Model\Product\ProductRepository */

		$productName = $productRepository->getById($id)->getName();
		$this->get('ss6.shop.product.product_edit_facade')->delete($id);

		$flashMessageTwig->addSuccess('Produkt <strong>{{ name }}</strong> byl smazán', array(
			'name' => $productName,
		));
		return $this->redirect($this->generateUrl('admin_product_list'));
	}
}
