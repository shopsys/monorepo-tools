<?php

namespace SS6\ShopBundle\Model\Product\TopProduct;

use SS6\ShopBundle\Component\Domain\SelectedDomain;
use SS6\ShopBundle\Component\Transformers\ProductIdToProductTransformer;
use SS6\ShopBundle\Component\Translation\Translator;
use SS6\ShopBundle\Form\Admin\Product\TopProduct\TopProductFormType;
use SS6\ShopBundle\Model\Grid\InlineEdit\AbstractGridInlineEdit;
use SS6\ShopBundle\Model\Product\TopProduct\TopProductData;
use SS6\ShopBundle\Model\Product\TopProduct\TopProductFacade;
use SS6\ShopBundle\Model\Product\TopProduct\TopProductGridFactory;
use Symfony\Component\Form\FormFactory;

class TopProductInlineEdit extends AbstractGridInlineEdit {

	/**
	 * @var \SS6\ShopBundle\Model\Product\TopProduct\TopProductFacade
	 */
	private $topProductFacade;

	/**
	 * @var \SS6\ShopBundle\Component\Transformers\ProductIdToProductTransformer
	 */
	private $productIdToProductTransformer;

	/**
	 * @var \SS6\ShopBundle\Component\Translation\Translator
	 */
	private $translator;

	/**
	 * @var \SS6\ShopBundle\Component\Domain\SelectedDomain
	 */
	private $selectedDomain;

	public function __construct(
		FormFactory $formFactory,
		TopProductGridFactory $topProductGridFactory,
		TopProductFacade $topProductFacade,
		ProductIdToProductTransformer $productIdToProductTransformer,
		Translator $translator,
		SelectedDomain $selectedDomain
	) {
		$this->topProductFacade = $topProductFacade;
		$this->productIdToProductTransformer = $productIdToProductTransformer;
		$this->translator = $translator;
		$this->selectedDomain = $selectedDomain;

		parent::__construct($formFactory, $topProductGridFactory);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\TopProduct\TopProductData $topProductData
	 * @return int
	 */
	protected function createEntityAndGetId($topProductData) {
		try {
			$topProduct = $this->topProductFacade->create($topProductData, $this->selectedDomain->getId());
		} catch (\SS6\ShopBundle\Model\Product\TopProduct\Exception\TopProductAlreadyExistsException $e) {
			throw new \SS6\ShopBundle\Model\Grid\InlineEdit\Exception\InvalidFormDataException(
				[
					$this->translator->trans('Tento produkt již v seznamu existuje.'),
				]
			);
		}
		return $topProduct->getId();
	}

	/**
	 * @param int $id
	 * @param \SS6\ShopBundle\Model\Product\TopProduct\TopProductData $topProductData
	 */
	protected function editEntity($id, $topProductData) {
		try {
			$this->topProductFacade->edit($id, $topProductData);
		} catch (\SS6\ShopBundle\Model\Product\TopProduct\Exception\TopProductAlreadyExistsException $e) {
			throw new \SS6\ShopBundle\Model\Grid\InlineEdit\Exception\InvalidFormDataException(
				[
					$this->translator->trans('Tento produkt již v seznamu existuje.'),
				]
			);
		}
	}

	/**
	 * @param int|null $id
	 * @return \SS6\ShopBundle\Model\Product\TopProduct\TopProductData
	 */
	protected function getFormDataObject($id = null) {
		$topProductData = new TopProductData();
		if ($id !== null) {
			$id = (int)$id;
			$topProduct = $this->topProductFacade->getById($id);
			$topProductData->setFromEntity($topProduct);
		}

		return $topProductData;
	}

	/**
	 * @param int $rowId
	 * @return \SS6\ShopBundle\Form\Admin\Product\TopProduct\TopProductFormType
	 */
	protected function getFormType($rowId) {
		return new TopProductFormType($this->productIdToProductTransformer);
	}
}
