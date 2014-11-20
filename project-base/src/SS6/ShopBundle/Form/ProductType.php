<?php

namespace SS6\ShopBundle\Form;

use SS6\ShopBundle\Component\Transformers\ProductIdToProductTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ProductType extends AbstractType {

	/**
	 * @var SS6\ShopBundle\Component\Transformers\ProductIdToProductTransformer
	 */
	private $productIdToProductTransformer;

	/**
	 * @param \SS6\ShopBundle\Component\Transformers\ProductIdToProductTransformer $productIdToProductTransformer
	 */
	public function __construct(ProductIdToProductTransformer $productIdToProductTransformer) {
		$this->productIdToProductTransformer = $productIdToProductTransformer;
	}

	/**
	 * @param \SS6\ShopBundle\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder->addModelTransformer($this->productIdToProductTransformer);
	}

	/**
	 * @return string
	 */
	public function getParent() {
		return 'number';
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'product';
	}
}