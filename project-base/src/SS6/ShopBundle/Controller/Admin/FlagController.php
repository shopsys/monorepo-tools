<?php

namespace SS6\ShopBundle\Controller\Admin;

use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Component\Router\Security\Annotation\CsrfProtection;
use SS6\ShopBundle\Controller\Admin\BaseController;
use SS6\ShopBundle\Model\Product\Flag\FlagFacade;
use SS6\ShopBundle\Model\Product\Flag\FlagInlineEdit;

class FlagController extends BaseController {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Flag\FlagFacade
	 */
	private $flagFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Flag\FlagInlineEdit
	 */
	private $flagInlineEdit;

	public function __construct(
		EntityManager $em,
		FlagFacade $flagFacade,
		FlagInlineEdit $flagInlineEdit
	) {
		$this->em = $em;
		$this->flagFacade = $flagFacade;
		$this->flagInlineEdit = $flagInlineEdit;
	}

	/**
	 * @Route("/product/flag/list/")
	 */
	public function listAction() {
		$productInlineEdit = $this->flagInlineEdit;

		$grid = $productInlineEdit->getGrid();

		return $this->render('@SS6Shop/Admin/Content/Flag/list.html.twig', [
			'gridView' => $grid->createView(),
		]);
	}

	/**
	 * @Route("/product/flag/delete/{id}", requirements={"id" = "\d+"})
	 * @CsrfProtection
	 * @param int $id
	 */
	public function deleteAction($id) {
		try {
			$fullName = $this->flagFacade->getById($id)->getName();
			$this->em->transactional(
				function () use ($id) {
					$this->flagFacade->deleteById($id);
				}
			);

			$this->getFlashMessageSender()->addSuccessFlashTwig('Příznak <strong>{{ name }}</strong> byl smazán', [
				'name' => $fullName,
			]);
		} catch (\SS6\ShopBundle\Model\Product\Flag\Exception\FlagNotFoundException $ex) {
			$this->getFlashMessageSender()->addErrorFlash('Zvolený příznak neexistuje.');
		}

		return $this->redirect($this->generateUrl('admin_flag_list'));
	}

}
