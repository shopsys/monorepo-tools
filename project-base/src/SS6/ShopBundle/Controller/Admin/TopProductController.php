<?php

namespace SS6\ShopBundle\Controller\Admin;

use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Component\Controller\AdminBaseController;
use SS6\ShopBundle\Component\Router\Security\Annotation\CsrfProtection;
use SS6\ShopBundle\Model\Product\TopProduct\TopProductFacade;
use SS6\ShopBundle\Model\Product\TopProduct\TopProductInlineEdit;

class TopProductController extends AdminBaseController {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Model\Product\TopProduct\TopProductFacade
	 */
	private $topProductFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Product\TopProduct\TopProductInlineEdit
	 */
	private $topProductInlineEdit;

	public function __construct(
		EntityManager $em,
		TopProductFacade $topProductFacade,
		TopProductInlineEdit $topProductInlineEdit
	) {
		$this->em = $em;
		$this->topProductFacade = $topProductFacade;
		$this->topProductInlineEdit = $topProductInlineEdit;
	}

	/**
	 * @Route("/product/top-product/list/")
	 */
	public function listAction() {
		$grid = $this->topProductInlineEdit->getGrid();

		return $this->render('@SS6Shop/Admin/Content/TopProduct/list.html.twig', [
			'gridView' => $grid->createView(),
		]);
	}

	/**
	 * @Route("/product/top-product/delete/{id}", requirements={"id" = "\d+"})
	 * @CsrfProtection
	 * @param int $id
	 */
	public function deleteAction($id) {
		try {
			$this->topProductFacade->getById($id);
			$this->em->transactional(
				function () use ($id) {
					$this->topProductFacade->deleteById($id);
				}
			);

			$this->getFlashMessageSender()->addSuccessFlashTwig('Zboží bylo odstraněno');
		} catch (\SS6\ShopBundle\Model\Product\TopProduct\Exception\TopProductNotFoundException $e) {
			$this->getFlashMessageSender()->addErrorFlashTwig('Zboží se nepodařilo odstranit - zboží není v tomto seznamu.');
		}

		return $this->redirect($this->generateUrl('admin_topproduct_list'));
	}
}
