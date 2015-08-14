<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Component\Controller\AdminBaseController;
use SS6\ShopBundle\Form\Admin\PromoCode\PromoCodeFormType;
use SS6\ShopBundle\Model\Order\PromoCode\PromoCodeFacade;
use Symfony\Component\HttpFoundation\Request;

class PromoCodeController extends AdminBaseController {

	/**
	 * @var \SS6\ShopBundle\Model\Order\PromoCode\PromoCodeFacade
	 */
	private $promoCodeFacade;

	public function __construct(PromoCodeFacade $promoCodeFacade) {
		$this->promoCodeFacade = $promoCodeFacade;
	}

	/**
	 * @Route("/promo-code/")
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function indexAction(Request $request) {
		$form = $this->createForm(new PromoCodeFormType());
		$form->setData([
			'code' => $this->promoCodeFacade->getPromoCode(),
			'percent' => $this->promoCodeFacade->getPromoCodePercent(),
		]);

		$form->handleRequest($request);

		if ($form->isValid()) {
			$formData = $form->getData();
			$this->promoCodeFacade->editPromoCode($formData['code'], $formData['percent']);
			$this->getFlashMessageSender()->addSuccessFlash('Nastavení slevového kupónu bylo uloženo.');

			return $this->redirectToRoute('admin_promocode_index');
		}

		return $this->render('@SS6Shop/Admin/Content/PromoCode/index.html.twig', [
			'form' => $form->createView(),
		]);
	}

}
