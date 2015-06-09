<?php

namespace SS6\ShopBundle\Form\Admin\Pricing\Group;

use SS6\ShopBundle\Form\FormType;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroupData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class PricingGroupFormType extends AbstractType {

	/**
	 * @return string
	 */
	public function getName() {
		return 'pricing_group_form';
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('name', FormType::TEXT, [
				'required' => false,
				'constraints' => [
					new Constraints\NotBlank(['message' => 'Vyplňte prosím název cenové skupiny']),
				],
			])
			->add('coefficient', FormType::NUMBER, [
				'required' => true,
				'precision' => 4,
				'invalid_message' => 'Prosím zadejte koeficient v platném formátu',
				'constraints' => [
					new Constraints\NotBlank(['message' => 'Vyplňte prosím koeficient cenové skupiny']),
					new Constraints\GreaterThan(['value' => 0, 'message' => 'Koeficient musí být větší než 0']),
				],
			]);
	}

	/**
	 * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults([
			'data_class' => PricingGroupData::class,
			'attr' => ['novalidate' => 'novalidate'],
		]);
	}
}
