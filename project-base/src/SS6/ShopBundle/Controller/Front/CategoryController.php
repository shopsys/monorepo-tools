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

	/**
	 * @param int $parentCategoryId
	 */
	public function panelAction($parentCategoryId = null) {
		if ($parentCategoryId === null) {
			$parentCategory = $this->categoryFacade->getRootCategory();
		} else {
			$parentCategory = $this->categoryFacade->getById($parentCategoryId);
		}

		$categoryDetails = $this->categoryFacade->getVisibleCollapsibleCategoryDetailsForParent(
			$parentCategory,
			$this->domain->getCurrentDomainConfig()
		);

		return $this->render('@SS6Shop/Front/Content/Category/panel.html.twig', [
			'collapsibleCategoryDetails' => $categoryDetails,
		]);
	}

}
