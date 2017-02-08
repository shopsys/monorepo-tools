<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Component\Controller\AdminBaseController;
use SS6\ShopBundle\Component\Domain\SelectedDomain;
use SS6\ShopBundle\Form\Admin\BestsellingProduct\BestsellingProductFormType;
use SS6\ShopBundle\Model\AdminNavigation\Breadcrumb;
use SS6\ShopBundle\Model\AdminNavigation\MenuItem;
use SS6\ShopBundle\Model\Category\CategoryFacade;
use SS6\ShopBundle\Model\Product\BestsellingProduct\BestsellingProductEditFacade;
use Symfony\Component\HttpFoundation\Request;

class BestsellingProductController extends AdminBaseController {

	/**
	 * @var \SS6\ShopBundle\Model\AdminNavigation\Breadcrumb
	 */
	private $breadcrumb;

	/**
	 * @var \SS6\ShopBundle\Model\Category\CategoryFacade
	 */
	private $categoryFacade;

	/**
	 * @var \SS6\ShopBundle\Component\Domain\SelectedDomain
	 */
	private $selectedDomain;

	/**
	 * @var \SS6\ShopBundle\Model\Product\BestsellingProduct\BestsellingProductEditFacade
	 */
	private $bestsellingProductEditFacade;

	public function __construct(
		BestsellingProductEditFacade $bestsellingProductEditFacade,
		CategoryFacade $categoryFacade,
		SelectedDomain $selectedDomain,
		Breadcrumb $breadcrumb
	) {
		$this->bestsellingProductEditFacade = $bestsellingProductEditFacade;
		$this->categoryFacade = $categoryFacade;
		$this->selectedDomain = $selectedDomain;
		$this->breadcrumb = $breadcrumb;
	}

	/**
	 * @Route("/product/bestselling-product/list/")
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function listAction(Request $request) {
		$domainId = $this->selectedDomain->getId();

		$categoryDetails = $this->categoryFacade->getVisibleCategoryDetailsForDomain($domainId, $request->getLocale());

		$bestsellingProductsInCategories = $this->bestsellingProductEditFacade
			->getManualBestsellingProductCountsInCategories($domainId);

		return $this->render('@SS6Shop/Admin/Content/BestsellingProduct/list.html.twig', [
			'categoryDetails' => $categoryDetails,
			'selectedDomainId' => $domainId,
			'bestsellingProductsInCategories' => $bestsellingProductsInCategories,
		]);
	}

	/**
	 * @Route("/product/bestselling-product/detail/")
	 */
	public function detailAction(Request $request) {
		$form = $this->createForm(new BestsellingProductFormType());

		$category = $this->categoryFacade->getById($request->get('categoryId'));
		$domainId = $request->get('domainId');

		$bestsellingProducts = $this->bestsellingProductEditFacade->getBestsellingProductsIndexedByPosition(
			$category,
			$domainId
		);

		$form->setData(['bestsellingProducts' => $bestsellingProducts]);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$formBestsellingProducts = $form->getData()['bestsellingProducts'];

			$this->bestsellingProductEditFacade->edit($category, $domainId, $formBestsellingProducts);

			$this->getFlashMessageSender()
				->addSuccessFlashTwig(
					t('Best-selling products of category <strong><a href="{{ url }}">{{ name }}</a></strong> set.'),
					[
						'name' => $category->getName(),
						'url' => $this->generateUrl(
							'admin_bestsellingproduct_detail',
							['domainId' => $domainId, 'categoryId' => $category->getId()]
						),
					]
				);
			return $this->redirectToRoute('admin_bestsellingproduct_list');
		}

		if ($form->isSubmitted() && !$form->isValid()) {
			$this->getFlashMessageSender()->addErrorFlashTwig(t('Please check the correctness of all data filled.'));
		}

		$this->breadcrumb->overrideLastItem(new MenuItem($category->getName()));

		return $this->render('@SS6Shop/Admin/Content/BestsellingProduct/detail.html.twig', [
			'form' => $form->createView(),
			'categoryName' => $category->getName(),
		]);
	}

}
