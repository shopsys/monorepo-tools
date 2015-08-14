<?php

namespace SS6\ShopBundle\Controller\Front;

use SS6\ShopBundle\Controller\Front\BaseController;
use SS6\ShopBundle\Model\Category\CategoryFacade;
use SS6\ShopBundle\Model\Domain\Domain;

class CategoryController extends BaseController {

	/**
	 * @var \SS6\ShopBundle\Model\Category\CategoryFacade
	 */
	private $categoryFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Domain\Domain
	 */
	private $domain;

	public function __construct(
		Domain $domain,
		CategoryFacade $categoryFacade
	) {
		$this->domain = $domain;
		$this->categoryFacade = $categoryFacade;
	}

	public function panelAction() {
		$categoryDetails = $this->categoryFacade->getVisibleCategoryDetailsForDomain(
			$this->domain->getId(),
			$this->domain->getLocale()
		);

		return $this->render('@SS6Shop/Front/Content/Category/panel.html.twig', [
			'categoryDetails' => $categoryDetails,
		]);
	}

}
