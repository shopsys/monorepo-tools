<?php

namespace SS6\ShopBundle\Controller\Front;

use SS6\ShopBundle\Component\Controller\FrontBaseController;
use SS6\ShopBundle\Component\Domain\Domain;
use SS6\ShopBundle\Model\Category\CategoryFacade;

class CategoryController extends FrontBaseController {

	/**
	 * @var \SS6\ShopBundle\Model\Category\CategoryFacade
	 */
	private $categoryFacade;

	/**
	 * @var \SS6\ShopBundle\Component\Domain\Domain
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
		$categoryDetails = $this->categoryFacade->getVisibleFirstLevelCategoryDetailsForDomain(
			$this->domain->getId(),
			$this->domain->getLocale()
		);

		return $this->render('@SS6Shop/Front/Content/Category/panel.html.twig', [
			'categoryDetails' => $categoryDetails,
		]);
	}

}
