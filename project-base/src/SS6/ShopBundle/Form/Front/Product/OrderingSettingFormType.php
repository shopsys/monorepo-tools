<?php

namespace SS6\ShopBundle\Form\Front\Product;

use SS6\ShopBundle\Form\FormType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class OrderingSettingFormType extends AbstractType {

	/**
	 * @var string[]
	 */
	private $orderingModesWithNames;

	public function __construct($orderingModesWithNames) {
		$this->orderingModesWithNames = $orderingModesWithNames;
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('orderingMode', FormType::CHOICE, [
				'choices' => $this->orderingModesWithNames,
			]);
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'productListOrderingSetting';
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
