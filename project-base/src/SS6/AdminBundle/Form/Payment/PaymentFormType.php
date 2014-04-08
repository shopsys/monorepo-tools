<?php

namespace SS6\AdminBundle\Form\Payment;

use SS6\CoreBundle\Form\YesNoType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PaymentFormType extends AbstractType {
	
	public function getName() {
		return 'payment';
	}

	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('id', 'integer', array('read_only' => true))
			->add('name', 'text')
			->add('hidden', new YesNoType(), array('required' => false))
			->add('price', 'money', array('currency' => false, 'required' => true))
			->add('description', 'textarea', array('required' => false))
			->add('save', 'submit');
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array(
			'data_class' => 'SS6\AdminBundle\Form\Payment\PaymentFormData',
		));
	}
}
