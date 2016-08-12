<?php

namespace SS6\ShopBundle\Controller\Front;

use SS6\ShopBundle\Component\Category\CurrentCategoryResolver;
use SS6\ShopBundle\Component\Controller\FrontBaseController;
use SS6\ShopBundle\Component\Domain\Domain;
use SS6\ShopBundle\Model\Category\CategoryFacade;
use Symfony\Component\HttpFoundation\Request;

class CategoryController extends FrontBaseController {

	/**
	 * @var \SS6\ShopBundle\Model\Category\CategoryFacade
	 */
	private $categoryFacade;

	/**
	 * @var \SS6\ShopBundle\Component\Domain\Domain
	 */
	private $domain;

	/**
	 * @var \SS6\ShopBundle\Component\Category\CurrentCategoryResolver
	 */
	private $currentCategoryResolver;

	public function __construct(
		Domain $domain,
		CategoryFacade $categoryFacade,
		CurrentCategoryResolver $currentCategoryResolver
	) {
		$this->domain = $domain;
		$this->categoryFacade = $categoryFacade;
		$this->currentCategoryResolver = $currentCategoryResolver;
	}

	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function panelAction(Request $request) {
		$categoryDetails = $this->categoryFacade->getVisibleCollapsibleCategoryDetailsForParent(
			$this->categoryFacade->getRootCategory(),
			$this->domain->getCurrentDomainConfig()
		);
		$currentCategory = $this->currentCategoryResolver->findCurrentCategoryByRequest($request, $this->domain->getId());

		if ($currentCategory !== null) {
			$openCategories = $this->categoryFacade->getVisibleCategoriesInPathFromRootOnDomain(
				$currentCategory,
				$this->domain->getId()
			);
		} else {
			$openCategories = [];
		}

		return $this->render('@SS6Shop/Front/Content/Category/panel.html.twig', [
			'collapsibleCategoryDetails' => $categoryDetails,
			'isRootParentCategory' => true,
			'openCategories' => $openCategories,
			'currentCategory' => $currentCategory,
		]);
	}

	/**
	 * @param int $parentCategoryId
	 */
	public function branchAction($parentCategoryId) {
		$parentCategory = $this->categoryFacade->getById($parentCategoryId);

		$categoryDetails = $this->categoryFacade->getVisibleCollapsibleCategoryDetailsForParent(
			$parentCategory,
			$this->domain->getCurrentDomainConfig()
		);

		return $this->render('@SS6Shop/Front/Content/Category/panel.html.twig', [
			'collapsibleCategoryDetails' => $categoryDetails,
			'isRootParentCategory' => false,
			'openCategories' => [],
			'currentCategory' => null,
		]);
	}

}
