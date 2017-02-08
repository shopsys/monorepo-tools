<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Component\Controller\AdminBaseController;
use SS6\ShopBundle\Component\Router\Security\Annotation\CsrfProtection;
use SS6\ShopBundle\Model\Order\PromoCode\Grid\PromoCodeInlineEdit;
use SS6\ShopBundle\Model\Order\PromoCode\PromoCodeFacade;

class PromoCodeController extends AdminBaseController {

	/**
	 * @var \SS6\ShopBundle\Model\Order\PromoCode\PromoCodeFacade
	 */
	private $promoCodeFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Order\PromoCode\Grid\PromoCodeInlineEdit
	 */
	private $promoCodeInlineEdit;

	public function __construct(
		PromoCodeFacade $promoCodeFacade,
		PromoCodeInlineEdit $promoCodeInlineEdit
	) {
		$this->promoCodeFacade = $promoCodeFacade;
		$this->promoCodeInlineEdit = $promoCodeInlineEdit;
	}

	/**
	 * @Route("/promo-code/list")
	 */
	public function listAction() {
		$grid = $this->promoCodeInlineEdit->getGrid();

		return $this->render('@SS6Shop/Admin/Content/PromoCode/list.html.twig', [
			'gridView' => $grid->createView(),
		]);
	}

	/**
	 * @Route("/promo-code/delete/{id}", requirements={"id" = "\d+"})
	 * @CsrfProtection
	 * @param int $id
	 */
	public function deleteAction($id) {
		try {
			$code = $this->promoCodeFacade->getById($id)->getCode();

			$this->promoCodeFacade->deleteById($id);

			$this->getFlashMessageSender()->addSuccessFlashTwig(
				t('Discount coupon <strong>{{ code }}</strong> deleted.'),
				[
					'code' => $code,
				]
			);
		} catch (\SS6\ShopBundle\Model\Order\PromoCode\Exception\PromoCodeNotFoundException $ex) {
			$this->getFlashMessageSender()->addErrorFlash(t('Selected discount coupon doesn\'t exist.'));
		}

		return $this->redirectToRoute('admin_promocode_list');
	}

}
