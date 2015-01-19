<?php

namespace SS6\ShopBundle\Form\Admin\Product\TopProduct;

use SS6\ShopBundle\Model\Product\TopProduct\TopProductData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class TopProductFormType extends AbstractType {

	/**
	 * @return string
	 */
	public function getName() {
		return 'top_products';
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add(
				'product', 'product', [
					'required' => true,
					'constraints' => [
						new Constraints\NotBlank(['message' => 'Vyplňte prosím ID produktu']),
					],
					'invalid_message' => 'Zadejte platné ID produktu',
				]
			);
	}

	/**
	 * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults([
			'data_class' => TopProductData::class,
			'attr' => ['novalidate' => 'novalidate'],
		]);
	}

}
