<?php

namespace SS6\ShopBundle\Controller\Front;

use SS6\ShopBundle\Controller\Front\BaseController;
use SS6\ShopBundle\Model\Advert\AdvertEditFacade;

class AdvertController extends BaseController {

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
		$advert = $this->advertEditFacade->getRandomAdvertByPositionOnCurrenctDomain($positionName);

		return $this->render('@SS6Shop/Front/Content/Advert/box.html.twig', [
			'advert' => $advert,
		]);
	}

}
