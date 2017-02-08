<?php

namespace Shopsys\ShopBundle\Form\Admin\Product\TopProduct;

use Shopsys\ShopBundle\Component\Transformers\RemoveDuplicatesFromArrayTransformer;
use Shopsys\ShopBundle\Form\FormType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TopProductsFormType extends AbstractType {

	/**
	 * @var \Shopsys\ShopBundle\Component\Transformers\RemoveDuplicatesFromArrayTransformer
	 */
	private $removeDuplicatesTransformer;

	/**
	 * @param \Shopsys\ShopBundle\Component\Transformers\RemoveDuplicatesFromArrayTransformer $removeDuplicatesTransformer
	 */
	public function __construct(RemoveDuplicatesFromArrayTransformer $removeDuplicatesTransformer) {
		$this->removeDuplicatesTransformer = $removeDuplicatesTransformer;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'top_products_form';
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add(
				$builder
					->create('products', FormType::PRODUCTS, [
						'required' => false,
						'sortable' => true,
					])
					->addViewTransformer($this->removeDuplicatesTransformer)
			)
			->add('save', FormType::SUBMIT);
	}

	/**
	 * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults([
			'attr' => ['novalidate' => 'novalidate'],
		]);
	}

}
