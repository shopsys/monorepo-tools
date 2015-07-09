<?php

namespace SS6\ShopBundle\Form\Admin\Product;

use SS6\ShopBundle\Component\Transformers\RemoveDuplicatesFromArrayTransformer;
use SS6\ShopBundle\Form\FormType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class VariantFormType extends AbstractType {

	const MAIN_VARIANT = 'mainVariant';
	const VARIANTS = 'variants';

	/**
	 * @return string
	 */
	public function getName() {
		return 'variant_form';
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add(self::MAIN_VARIANT, FormType::PRODUCT, [
				'constraints' => [
					new Constraints\NotBlank(),
				],
			])
			->add(
				$builder
					->create(self::VARIANTS, FormType::PRODUCTS, [
						'constraints' => [
							new Constraints\NotBlank(),
						],
					])
					->addViewTransformer(new RemoveDuplicatesFromArrayTransformer())
			)
			->add('save', FormType::SUBMIT);
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults([
			'attr' => ['novalidate' => 'novalidate'],
		]);
	}

}
