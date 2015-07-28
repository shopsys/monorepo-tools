<?php

namespace SS6\ShopBundle\Form\Admin\PromoCode;

use SS6\ShopBundle\Form\FormType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class PromoCodeFormType extends AbstractType {

	/**
	 * @return string
	 */
	public function getName() {
		return 'promo_code_form';
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('code', FormType::TEXT, [
				'required' => false,
			])
			->add('percent', FormType::NUMBER, [
				'required' => false,
				'constraints' => [
					new Constraints\Range([
						'min' => 0,
						'max' => 100,
					]),
				],
			])
			->add('save', FormType::SUBMIT);
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults([
			'attr' => ['novalidate' => 'novalidate'],
		]);
	}

}
