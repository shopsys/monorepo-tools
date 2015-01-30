<?php

namespace SS6\ShopBundle\Form\Admin\CustomerCommunication;

use SS6\ShopBundle\Form\FormType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class CustomerCommunicationFormType extends AbstractType {

	/**
	 * @return string
	 */
	public function getName() {
		return 'customer_communication';
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('content', FormType::CKEDITOR, ['required' => false])
			->add('save', 'submit');
	}
}
