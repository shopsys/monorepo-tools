<?php

namespace SS6\ShopBundle\Model\Product\TopProduct;

use SS6\ShopBundle\Component\Transformers\ProductIdToProductTransformer;
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
	 * @param \Symfony\Component\Form\FormFactory $formFactory
	 * @param \SS6\ShopBundle\Model\Product\TopProduct\TopProductGridFactory $topProductGridFactory
	 * @param \SS6\ShopBundle\Model\Product\TopProduct\TopProductFacade $topProductFacade
	 */
	public function __construct(
		FormFactory $formFactory,
		TopProductGridFactory $topProductGridFactory,
		TopProductFacade $topProductFacade,
		ProductIdToProductTransformer $productIdToProductTransformer
	) {
		$this->topProductFacade = $topProductFacade;
		$this->productIdToProductTransformer = $productIdToProductTransformer;

		parent::__construct($formFactory, $topProductGridFactory);
	}

	/**
	 * @return string
	 */
	public function getServiceName() {
		return 'ss6.shop.product.top_product.top_product_inline_edit';
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\TopProduct\TopProductData $topProductData
	 * @return int
	 */
	protected function createEntityAndGetId($topProductData) {
		try {
			$topProduct = $this->topProductFacade->create($topProductData);
		} catch (\SS6\ShopBundle\Model\Product\TopProduct\Exception\TopProductAlreadyExistsException $e) {
			throw new \SS6\ShopBundle\Model\Grid\InlineEdit\Exception\InvalidFormDataException(
				['Tento produkt již v seznamu existuje.']
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
				['Tento produkt již v seznamu existuje.']
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
