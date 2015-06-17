<?php

namespace SS6\ShopBundle\Controller\Admin;

use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Controller\Admin\BaseController;
use SS6\ShopBundle\Form\Admin\BestsellingProduct\BestsellingProductFormType;
use SS6\ShopBundle\Model\AdminNavigation\Breadcrumb;
use SS6\ShopBundle\Model\AdminNavigation\MenuItem;
use SS6\ShopBundle\Model\Category\CategoryFacade;
use SS6\ShopBundle\Model\Domain\SelectedDomain;
use SS6\ShopBundle\Model\Product\BestsellingProduct\BestsellingProductFacade;
use Symfony\Component\HttpFoundation\Request;

class BestsellingProductController extends BaseController {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Model\AdminNavigation\Breadcrumb
	 */
	private $breadcrumb;

	/**
	 * @var \SS6\ShopBundle\Model\Category\CategoryFacade
	 */
	private $categoryFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Domain\SelectedDomain
	 */
	private $selectedDomain;

	/**
	 * @var \SS6\ShopBundle\Model\Product\BestsellingProduct\BestsellingProductFacade
	 */
	private $bestsellingProductFacade;

	public function __construct(
		BestsellingProductFacade $bestsellingProductFacade,
		CategoryFacade $categoryFacade,
		SelectedDomain $selectedDomain,
		Breadcrumb $breadcrumb,
		EntityManager $em
	) {
		$this->bestsellingProductFacade = $bestsellingProductFacade;
		$this->categoryFacade = $categoryFacade;
		$this->selectedDomain = $selectedDomain;
		$this->breadcrumb = $breadcrumb;
		$this->em = $em;
	}

	/**
	 * @Route("/product/bestselling_product/list/")
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function listAction(Request $request) {
		$domainId = $this->selectedDomain->getId();

		$categoryDetails = $this->categoryFacade->getVisibleCategoryDetailsForDomain($domainId, $request->getLocale());

		$bestsellingProductsInCategories = $this->bestsellingProductFacade->getManualBestsellingProductCountsInCategories($domainId);

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

			$this->em->transactional(
				function () use ($category, $domainId, $formBestsellingProducts) {
					$this->bestsellingProductFacade->edit(
						$category,
						$domainId,
						$formBestsellingProducts
					);
				}
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
