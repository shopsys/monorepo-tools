<?php

namespace SS6\ShopBundle\Form\Admin\Transport;

use SS6\ShopBundle\Form\Admin\Transport\TransportFormData;
use SS6\ShopBundle\Form\YesNoType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class TransportFormType extends AbstractType {

	/**
	 * @return string
	 */
	public function getName() {
		return 'transport';
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('id', 'integer', array('read_only' => true))
			->add('name', 'text', array(
				'constraints' => array(
					new Constraints\NotBlank(array('message' => 'Prosím vyplňte název')),
				),
			))
			->add('hidden', new YesNoType(), array('required' => false))
			->add('price', 'money', array(
				'currency' => false,
				'required' => true,
				'constraints' => array(
					new Constraints\NotBlank(array('message' => 'Prosím vyplňte cenu')),
				),
				'invalid_message' => 'Prosím zadejte cenu v platném formátu',
			))
			->add('description', 'textarea', array('required' => false))
			->add('save', 'submit');
	}

	/**
	 * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array(
			'data_class' => TransportFormData::class,
			'attr' => array('novalidate' => 'novalidate'),
		));
	}
}
