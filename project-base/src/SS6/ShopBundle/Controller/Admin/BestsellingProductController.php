<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Controller\Admin\BaseController;
use SS6\ShopBundle\Form\Admin\BestsellingProduct\BestsellingProductFormType;
use SS6\ShopBundle\Model\AdminNavigation\Breadcrumb;
use SS6\ShopBundle\Model\AdminNavigation\MenuItem;
use SS6\ShopBundle\Model\Category\CategoryFacade;
use SS6\ShopBundle\Model\Domain\Domain;
use SS6\ShopBundle\Model\Domain\SelectedDomain;
use SS6\ShopBundle\Model\Localization\Localization;
use SS6\ShopBundle\Model\Product\BestsellingProduct\BestsellingProductFacade;
use SS6\ShopBundle\Model\Product\BestsellingProduct\BestsellingProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class BestsellingProductController extends BaseController {

	/**
	 * @var Breadcrumb
	 */
	private $breadcrumb;

	/**
	 * @var SelectedDomain
	 */
	private $selectedDomain;

	/**
	 * @var Localization
	 */
	private $localization;

	/**
	 * @var CategoryFacade
	 */
	private $categoryFacade;

	/**
	 * @var Domain
	 */
	private $domain;

	/**
	 * @var Session
	 */
	private $session;

	/**
	 * @var \SS6\ShopBundle\Model\Product\BestsellingProduct\BestsellingProductFacade
	 */
	private $bestsellingProductFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Product\BestsellingProduct\BestsellingProductRepository
	 */
	private $bestsellingProductRepository;

	public function __construct(
		BestsellingProductFacade $bestsellingProductFacade,
		Session $session,
		Domain $domain,
		CategoryFacade $categoryFacade,
		Localization $localization,
		SelectedDomain $selectedDomain,
		Breadcrumb $breadcrumb,
		BestsellingProductRepository $bestsellingProductRepository
	) {
		$this->bestsellingProductFacade = $bestsellingProductFacade;
		$this->session = $session;
		$this->domain = $domain;
		$this->categoryFacade = $categoryFacade;
		$this->localization = $localization;
		$this->selectedDomain = $selectedDomain;
		$this->breadcrumb = $breadcrumb;
		$this->bestsellingProductRepository = $bestsellingProductRepository;
	}

	/**
	 * @Route("/product/bestselling_product/list/")
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function listAction(Request $request) {
		$domainId = $this->selectedDomain->getId();

		$categoryDetails = $this->categoryFacade->getVisibleCategoryDetailsForDomain($domainId, $request->getLocale());

		$bestsellingProductsInCategories =
			$this->bestsellingProductRepository->getManualBestsellingProductCountsInCategories($domainId);

		return $this->render('@SS6Shop/Admin/Content/BestsellingProduct/list.html.twig', [
			'categoryDetails' => $categoryDetails,
			'selectedDomainId' => $domainId,
			'bestsellingProductsInCategories' => $bestsellingProductsInCategories,
		]);
	}

	/**
	 * @Route("/product/bestselling_product/detail/")
	 */
	public function detailAction(Request $request) {
		$form = $this->createForm(new BestsellingProductFormType());

		$category = $this->categoryFacade->getById($request->get('categoryId'));
		$domainId = $request->get('domainId');

		$bestsellingProducts = $this->bestsellingProductFacade->getBestsellingProductsIndexedByPosition(
			$category->getId(),
			$domainId
		);

		$form->setData(['bestsellingProducts' => $bestsellingProducts]);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$formBestsellingProducts = $form->getData()['bestsellingProducts'];

			$this->bestsellingProductFacade->edit(
				$category,
				$domainId,
				$formBestsellingProducts
			);

			$this->getFlashMessageSender()
				->addSuccessFlashTwig(
					'Bylo nastaveno nejprodávanější zboží kategorie <strong><a href="{{ url }}">{{ name }}</a></strong>',
					[
						'name' => $category->getName(),
						'url' => $this->generateUrl(
							'admin_bestsellingproduct_detail',
							['domainId' => $domainId, 'categoryId' => $category->getId()]
						),
					]
				);
			return $this->redirect($this->generateUrl('admin_bestsellingproduct_list'));
		}

		$this->breadcrumb->replaceLastItem(new MenuItem($category->getName()));

		return $this->render('@SS6Shop/Admin/Content/BestsellingProduct/detail.html.twig', [
			'form' => $form->createView(),
			'categoryName' => $category->getName(),
		]);
	}

}