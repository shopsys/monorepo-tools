<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Component\Controller\AdminBaseController;
use SS6\ShopBundle\Component\Domain\SelectedDomain;
use SS6\ShopBundle\Form\Admin\Category\TopCategory\TopCategoriesFormTypeFactory;
use SS6\ShopBundle\Model\Category\TopCategory\TopCategoryFacade;
use Symfony\Component\HttpFoundation\Request;

class TopCategoryController extends AdminBaseController {

	/**
	 * @var \SS6\ShopBundle\Model\Category\TopCategory\TopCategoryFacade
	 */
	private $topCategoryFacade;
	/**
	 * @var \SS6\ShopBundle\Form\Admin\Category\TopCategory\TopCategoriesFormTypeFactory
	 */
	private $topCategoriesFormTypeFactory;

	/**
	 * @var \SS6\ShopBundle\Component\Domain\SelectedDomain
	 */
	private $selectedDomain;

	public function __construct(
		TopCategoryFacade $topCategoryFacade,
		TopCategoriesFormTypeFactory $topCategoriesFormTypeFactory,
		SelectedDomain $selectedDomain
	) {
		$this->topCategoryFacade = $topCategoryFacade;
		$this->topCategoriesFormTypeFactory = $topCategoriesFormTypeFactory;
		$this->selectedDomain = $selectedDomain;
	}

	/**
	 * @Route("/category/top-category/list/")
	 */
	public function listAction(Request $request) {
		$domainConfig = $this->selectedDomain->getCurrentSelectedDomain();

		$form = $this->createForm($this->topCategoriesFormTypeFactory->create($domainConfig));
		$formData = [
			'categories' => $this->topCategoryFacade->getCategoriesForAll($domainConfig->getId()),
		];

		$form->setData($formData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$categories = $form->getData()['categories'];

			$this->topCategoryFacade->saveTopCategoriesForDomain($domainConfig->getId(), $categories);

			$this->getFlashMessageSender()->addSuccessFlash(t('Nastavení zboží na titulce bylo úspěšně změněno.'));
		}

		return $this->render('@SS6Shop/Admin/Content/TopCategory/list.html.twig', [
			'form' => $form->createView(),
		]);
	}

}
