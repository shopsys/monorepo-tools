<?php

namespace SS6\ShopBundle\Form\Admin\Product\TopProduct;

use SS6\ShopBundle\Component\Transformers\RemoveDuplicatesFromArrayTransformer;
use SS6\ShopBundle\Form\FormType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TopProductsFormType extends AbstractType {

	/**
	 * @var \SS6\ShopBundle\Component\Transformers\RemoveDuplicatesFromArrayTransformer
	 */
	private $removeDuplicatesTransformer;

	/**
	 * @param \SS6\ShopBundle\Component\Transformers\RemoveDuplicatesFromArrayTransformer $removeDuplicatesTransformer
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
