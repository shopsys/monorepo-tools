<?php

namespace SS6\ShopBundle\Controller\Admin;

use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Component\Controller\AdminBaseController;
use SS6\ShopBundle\Component\Router\Security\Annotation\CsrfProtection;
use SS6\ShopBundle\Model\Order\PromoCode\Grid\PromoCodeInlineEdit;
use SS6\ShopBundle\Model\Order\PromoCode\PromoCodeFacade;

class PromoCodeController extends AdminBaseController {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Model\Order\PromoCode\PromoCodeFacade
	 */
	private $promoCodeFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Order\PromoCode\Grid\PromoCodeInlineEdit
	 */
	private $promoCodeInlineEdit;

	public function __construct(
		EntityManager $em,
		PromoCodeFacade $promoCodeFacade,
		PromoCodeInlineEdit $promoCodeInlineEdit
	) {
		$this->em = $em;
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
			$this->em->transactional(
				function () use ($id) {
					$this->promoCodeFacade->deleteById($id);
				}
			);

			$this->getFlashMessageSender()->addSuccessFlashTwig('Slevový kupón <strong>{{ code }}</strong> byl smazán', [
				'code' => $code,
			]);
		} catch (\SS6\ShopBundle\Model\Order\PromoCode\Exception\PromoCodeNotFoundException $ex) {
			$this->getFlashMessageSender()->addErrorFlash('Zvolený slevový kupón neexistuje.');
		}

		return $this->redirectToRoute('admin_promocode_list');
	}

}
