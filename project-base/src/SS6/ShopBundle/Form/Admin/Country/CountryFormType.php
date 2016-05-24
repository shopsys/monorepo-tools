<?php

namespace SS6\ShopBundle\Form\Admin\Country;

use SS6\ShopBundle\Form\FormType;
use SS6\ShopBundle\Model\Country\CountryData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class CountryFormType extends AbstractType {

	/**
	 * @return string
	 */
	public function getName() {
		return 'country_form';
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('name', FormType::TEXT, [
				'required' => true,
				'constraints' => [
					new Constraints\NotBlank(['message' => 'Vyplňte prosím název státu']),
					new Constraints\Length(['max' => 255, 'maxMessage' => 'Název státu nesmí být delší než {{ limit }} znaků']),
				],
			]);
	}

	/**
	 * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults([
			'data_class' => CountryData::class,
			'attr' => ['novalidate' => 'novalidate'],
		]);
	}

}
