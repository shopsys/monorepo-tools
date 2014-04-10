<?php

namespace SS6\AdminBundle\Controller;

use APY\DataGridBundle\Grid\Action\RowAction;
use APY\DataGridBundle\Grid\Grid;
use APY\DataGridBundle\Grid\Source\Entity;
use SS6\AdminBundle\Form\Product\ProductFormType;
use SS6\CoreBundle\Exception\ValidationException;
use SS6\CoreBundle\Model\Product\Exception\ProductNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends Controller {
	/**
	 * @param Request $request
	 * @param int $id
	 */
	public function editAction(Request $request, $id) {
		$form = $this->createForm(new ProductFormType());
						
		try {
			$result = $this->get('ss6.core.product.product_edit_facade')->edit($id, $request, $form);

			if ($result) {
				$this->get('session')->getFlashBag()->add(
					'success', 'Produkt byl úspěšně upraven.'
				);
				return $this->redirect($this->generateUrl('admin_product_edit', array('id' => $id)));
			}
		} catch (ProductNotFoundException $e) {
			throw $this->createNotFoundException($e->getMessage(), $e);
		} catch (ValidationException $e) {
			$this->get('session')->getFlashBag()->add(
				'error', $e->getMessage()
			);
		}
		
		return $this->render('SS6AdminBundle:Content/Product:edit.html.twig', array(
			'form' => $form->createView(),
			'product' => $form->getData(),
		));
	}
	
	/**
	 * @param Request $request
	 */
	public function newAction(Request $request) {
		$form = $this->createForm(new ProductFormType());
						
		try {
			$result = $this->get('ss6.core.product.product_edit_facade')->create($request, $form);

			if ($result) {
				$this->get('session')->getFlashBag()->add(
					'success', 'Produkt byl úspěšně vytvořen.'
				);
				return $this->redirect($this->generateUrl('admin_product_edit', array('id' => $form->getData()->getId())));
			}
		} catch (ProductNotFoundException $e) {
			throw $this->createNotFoundException($e->getMessage(), $e);
		} catch (ValidationException $e) {
			$this->get('session')->getFlashBag()->add(
				'error', $e->getMessage()
			);
		}
		
		return $this->render('SS6AdminBundle:Content/Product:new.html.twig', array(
			'form' => $form->createView(),
		));
	}
	
	public function listAction() {
		$source = new Entity('SS6CoreBundle:Product\Entity\Product');
				
		$grid = $this->get('grid');
		/* @var $grid Grid */
		$grid->setSource($source);
		
		$grid->setVisibleColumns(array('name', 'price'));
		$grid->getColumns()->getColumnById('name')->setTitle('Název');
		$grid->getColumns()->getColumnById('price')->setTitle('Cena');
		
		$grid->hideFilters();
		$grid->setActionsColumnTitle('Akce');
		$grid->setDefaultOrder('name', 'asc');
		$grid->setLimits(2);
		
		$detailRowAction = new RowAction('Detail', 'admin_product_edit');
		$detailRowAction->setRouteParameters(array('id'));
		$grid->addRowAction($detailRowAction);
		
		$deleteRowAction = new RowAction('Smazat', 'admin_product_delete', true);
		$deleteRowAction->setConfirmMessage('Opravdu si přejete zboží smazat?');
		$deleteRowAction->setRouteParameters(array('id'));
		$grid->addRowAction($deleteRowAction);
		
		return $grid->getGridResponse('SS6AdminBundle:Content/Product:list.html.twig');
	}
	
	/**
	 * @param int $id
	 */
	public function deleteAction($id) {
		try {
			$this->get('ss6.core.product.product_edit_facade')->delete($id);

			$this->get('session')->getFlashBag()->add(
				'success', 'Produkt byl úspěšně smazán.'
			);
		} catch (ProductNotFoundException $e) {
			throw $this->createNotFoundException($e->getMessage());
		}
		
		return $this->redirect($this->generateUrl('admin_product_list'));
	}
}
