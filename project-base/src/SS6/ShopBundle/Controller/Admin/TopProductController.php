<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Component\Controller\AdminBaseController;
use SS6\ShopBundle\Component\Domain\SelectedDomain;
use SS6\ShopBundle\Form\Admin\Product\TopProduct\TopProductsFormTypeFactory;
use SS6\ShopBundle\Model\Product\TopProduct\TopProductFacade;
use Symfony\Component\HttpFoundation\Request;

class TopProductController extends AdminBaseController {

	/**
	 * @var \SS6\ShopBundle\Model\Product\TopProduct\TopProductFacade
	 */
	private $topProductFacade;
	/**
	 * @var \SS6\ShopBundle\Form\Admin\Product\TopProduct\TopProductsFormTypeFactory
	 */
	private $topProductsFormTypeFactory;

	/**
	 * @var \SS6\ShopBundle\Component\Domain\SelectedDomain
	 */
	private $selectedDomain;

	public function __construct(
		TopProductFacade $topProductFacade,
		TopProductsFormTypeFactory $topProductsFormTypeFactory,
		SelectedDomain $selectedDomain
	) {
		$this->topProductFacade = $topProductFacade;
		$this->topProductsFormTypeFactory = $topProductsFormTypeFactory;
		$this->selectedDomain = $selectedDomain;
	}

	/**
	 * @Route("/product/top-product/list/")
	 */
	public function listAction(Request $request) {
		$form = $this->createForm($this->topProductsFormTypeFactory->create());

		$domainId = $this->selectedDomain->getId();
		$formData = [
			'products' => $this->getProductsForDomain($domainId),
		];

		$form->setData($formData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$products = $form->getData()['products'];

			$this->topProductFacade->saveTopProductsForDomain($domainId, $products);

			$this->getFlashMessageSender()->addSuccessFlash(t('Nastavení zboží na titulce bylo úspěšně změněno.'));
		}

		return $this->render('@SS6Shop/Admin/Content/TopProduct/list.html.twig', [
			'form' => $form->createView(),
		]);
	}

	/**
	 * @param int $domainId
	 * @return \SS6\ShopBundle\Model\Product\Product[]
	 */
	private function getProductsForDomain($domainId) {
		$topProducts = $this->topProductFacade->getAll($domainId);
		$products = [];

		foreach ($topProducts as $topProduct) {
			$products[] = $topProduct->getProduct();
		}

		return $products;
	}
}
