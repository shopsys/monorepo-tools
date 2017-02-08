<?php

namespace Shopsys\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Shopsys\ShopBundle\Component\Controller\AdminBaseController;
use Shopsys\ShopBundle\Component\Router\Security\Annotation\CsrfProtection;
use Shopsys\ShopBundle\Model\Product\Flag\FlagFacade;
use Shopsys\ShopBundle\Model\Product\Flag\FlagInlineEdit;

class FlagController extends AdminBaseController {

	/**
	 * @var \Shopsys\ShopBundle\Model\Product\Flag\FlagFacade
	 */
	private $flagFacade;

	/**
	 * @var \Shopsys\ShopBundle\Model\Product\Flag\FlagInlineEdit
	 */
	private $flagInlineEdit;

	public function __construct(
		FlagFacade $flagFacade,
		FlagInlineEdit $flagInlineEdit
	) {
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

			$this->flagFacade->deleteById($id);

			$this->getFlashMessageSender()->addSuccessFlashTwig(
				t('Flag <strong>{{ name }}</strong> deleted'),
				[
					'name' => $fullName,
				]
			);
		} catch (\Shopsys\ShopBundle\Model\Product\Flag\Exception\FlagNotFoundException $ex) {
			$this->getFlashMessageSender()->addErrorFlash(t('Selected flag doesn\'t exist.'));
		}

		return $this->redirectToRoute('admin_flag_list');
	}

}
