<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Component\Controller\AdminBaseController;
use SS6\ShopBundle\Component\Router\Security\Annotation\CsrfProtection;
use SS6\ShopBundle\Model\Product\Unit\UnitFacade;
use SS6\ShopBundle\Model\Product\Unit\UnitInlineEdit;

class UnitController extends AdminBaseController {

	/**
	 * @var \SS6\ShopBundle\Model\Product\Unit\UnitFacade
	 */
	private $unitFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Unit\UnitInlineEdit
	 */
	private $unitInlineEdit;

	public function __construct(
		UnitFacade $unitFacade,
		UnitInlineEdit $unitInlineEdit
	) {
		$this->unitFacade = $unitFacade;
		$this->unitInlineEdit = $unitInlineEdit;
	}

	/**
	 * @Route("/product/unit/list/")
	 */
	public function listAction() {
		$unitInlineEdit = $this->unitInlineEdit;

		$grid = $unitInlineEdit->getGrid();

		return $this->render('@SS6Shop/Admin/Content/Unit/list.html.twig', [
			'gridView' => $grid->createView(),
		]);
	}

	/**
	 * @Route("/product/unit/delete/{id}", requirements={"id" = "\d+"})
	 * @CsrfProtection
	 * @param int $id
	 */
	public function deleteAction($id) {
		try {
			$fullName = $this->unitFacade->getById($id)->getName();
			$this->transactional(
				function () use ($id) {
					$this->unitFacade->deleteById($id);
				}
			);

			$this->getFlashMessageSender()->addSuccessFlashTwig('Jednotka <strong>{{ name }}</strong> byla smazána', [
				'name' => $fullName,
			]);
		} catch (\SS6\ShopBundle\Model\Product\Unit\Exception\UnitNotFoundException $ex) {
			$this->getFlashMessageSender()->addErrorFlash('Zvolená jednotka neexistuje.');
		}

		return $this->redirect($this->generateUrl('admin_unit_list'));
	}

}
