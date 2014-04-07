<?php

namespace SS6\AdminBundle\Controller;

use SS6\AdminBundle\Form\Product\ProductFormType;
use SS6\CoreBundle\Model\Product\Exception\ProductNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends Controller {
	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
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
			throw $this->createNotFoundException($e->getMessage());
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
}
