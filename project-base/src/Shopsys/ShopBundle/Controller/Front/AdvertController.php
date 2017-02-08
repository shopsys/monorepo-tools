<?php

namespace SS6\ShopBundle\Controller\Front;

use SS6\ShopBundle\Component\Controller\FrontBaseController;
use SS6\ShopBundle\Model\Advert\AdvertEditFacade;

class AdvertController extends FrontBaseController {

	/**
	 * @var \SS6\ShopBundle\Model\Advert\AdvertEditFacade
	 */
	private $advertEditFacade;

	public function __construct(AdvertEditFacade $advertEditFacade) {
		$this->advertEditFacade = $advertEditFacade;
	}

	/**
	 * @param string $positionName
	 */
	public function boxAction($positionName) {
		$advert = $this->advertEditFacade->findRandomAdvertByPositionOnCurrentDomain($positionName);

		return $this->render('@SS6Shop/Front/Content/Advert/box.html.twig', [
			'advert' => $advert,
		]);
	}

}
