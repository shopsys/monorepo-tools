<?php

namespace SS6\ShopBundle\Controller\Front;

use SS6\ShopBundle\Component\Controller\FrontBaseController;
use SS6\ShopBundle\Model\Product\Brand\BrandFacade;

class BrandController extends FrontBaseController {

	/**
	 * @var \SS6\ShopBundle\Model\Product\Brand\BrandFacade
	 */
	private $brandFacade;

	public function __construct(
		BrandFacade $brandFacade
	) {
		$this->brandFacade = $brandFacade;
	}

	public function listAction() {
		return $this->render('@SS6Shop/Front/Content/Brand/list.html.twig', [
			'brands' => $this->brandFacade->getAll(),
		]);
	}

}
