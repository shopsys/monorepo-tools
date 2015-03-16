<?php

namespace SS6\ShopBundle\Form;

use SS6\ShopBundle\Component\Transformers\ProductIdToProductTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class ProductType extends AbstractType {

	/**
	 * @var \SS6\ShopBundle\Component\Transformers\ProductIdToProductTransformer
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
	 * @param \Symfony\Component\Form\FormView $view
	 * @param \Symfony\Component\Form\FormInterface $form
	 * @param array $options
	 */
	public function buildView(FormView $view, FormInterface $form, array $options) {
		parent::buildView($view, $form, $options);

		$product = $form->getData();
		if ($product !== null) {
			/* @var $product \SS6\ShopBundle\Model\Product\Product */
			$view->vars['productName'] = $product->getName();
		}
	}

	/**
	 * @return string
	 */
	public function getParent() {
		return 'hidden';
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'product';
	}

}