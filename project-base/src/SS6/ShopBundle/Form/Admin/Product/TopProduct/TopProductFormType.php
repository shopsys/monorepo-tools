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
				'product', 'product', array(
					'required' => true,
					'constraints' => array(
						new Constraints\NotBlank(array('message' => 'Vyplňte prosím id produktu')),
					)
				)
			);
	}

	/**
	 * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array(
			'data_class' => TopProductData::class,
			'attr' => array('novalidate' => 'novalidate'),
		));
	}

}
