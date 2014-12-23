<?php

namespace SS6\ShopBundle\Form\Admin\Product\Parameter;

use SS6\ShopBundle\Model\Product\Parameter\ParameterData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class ParameterFormType extends AbstractType {

	/**
	 * @return string
	 */
	public function getName() {
		return 'parameter';
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('name', 'localized', array(
				'required' => false,
				'options' => array(
					'constraints' => array(
						new Constraints\NotBlank(array('message' => 'Vyplňte prosím název parametru')),
						new Constraints\Length(array('max' => 100, 'maxMessage' => 'Název parametru nesmí být delší než {{ limit }} znaků')),
					)
				),
			));
	}

	/**
	 * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array(
			'data_class' => ParameterData::class,
			'attr' => array('novalidate' => 'novalidate'),
		));
	}

}
