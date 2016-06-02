<?php

namespace SS6\ShopBundle\Form\Admin\Product\Flag;

use SS6\ShopBundle\Form\FormType;
use SS6\ShopBundle\Model\Product\Flag\FlagData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class FlagFormType extends AbstractType {

	/**
	 * @return string
	 */
	public function getName() {
		return 'flag_form';
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('name', FormType::LOCALIZED, [
				'required' => true,
				'options' => [
					'constraints' => [
						new Constraints\NotBlank(['message' => 'Vyplňte prosím název příznaku ve všech jazycích']),
						new Constraints\Length(['max' => 100, 'maxMessage' => 'Název příznaku nesmí být delší než {{ limit }} znaků']),
					],
				],
			])
			->add('rgbColor', FormType::COLOR_PICKER, [
				'constraints' => [
					new Constraints\NotBlank(['message' => 'Vyplňte prosím barvu příznaku']),
					new Constraints\Length([
						'max' => 7,
						'maxMessage' => 'Barva příznaku se zadává v hexa kódu, například #3333ff. Nesmí být tedy delší než {{ limit }} znaků',
					]),
				],
			])
			->add('visible', FormType::CHECKBOX, ['required' => false]);
	}

	/**
	 * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults([
			'data_class' => FlagData::class,
			'attr' => ['novalidate' => 'novalidate'],
		]);
	}

}
