<?php

namespace Shopsys\ShopBundle\Controller\Front;

use Shopsys\ShopBundle\Component\Controller\FrontBaseController;
use Shopsys\ShopBundle\Model\Advert\AdvertEditFacade;

class AdvertController extends FrontBaseController {

	/**
	 * @var \Shopsys\ShopBundle\Model\Advert\AdvertEditFacade
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
