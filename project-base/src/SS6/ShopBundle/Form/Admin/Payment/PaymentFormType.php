<?php

namespace SS6\ShopBundle\Form\Admin\Payment;

use SS6\ShopBundle\Form\YesNoType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PaymentFormType extends AbstractType {
	
	private $allTransports;
	
	/**
	 * @param array $allTransports
	 */
	public function __construct(array $allTransports) {
		$this->allTransports = $allTransports;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'payment';
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$transportChoices = array();
		foreach ($this->allTransports as $transport) {
			/* @var $transport \SS6\ShopBundle\Model\Transport\Transport */
			$transportChoices[$transport->getId()] = $transport->getName();
		}
		
		$builder
			->add('id', 'integer', array('read_only' => true))
			->add('name', 'text')
			->add('hidden', new YesNoType(), array('required' => false))
			->add('transports', 'choice', array(
				'choices' => $transportChoices,
				'multiple' => true,
				'expanded' => true,
				'required' => false,
			))
			->add('price', 'money', array('currency' => false, 'required' => true))
			->add('description', 'textarea', array('required' => false))
			->add('save', 'submit');
	}

	/**
	 * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array(
			'data_class' => PaymentFormData::class,
		));
	}
}
