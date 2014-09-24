<?php

namespace SS6\ShopBundle\Form\Admin\Mail;

use SS6\ShopBundle\Model\Mail\MailTemplateData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class MailTemplateFormType extends AbstractType {
	
	/**
	 * @return string
	 */
	public function getName() {
		return 'mail_template';
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('subject', 'text', array(
				'constraints' => array(
					new Constraints\NotBlank(array('message' => 'Vyplňte prosím předmět')),
				),
			))
			->add('body', 'ckeditor', array(
				'constraints' => array(
					new Constraints\NotBlank(array('message' => 'Vyplňte prosím obsah emailu')),
				),
				'config_name' => 'email',
			))
			->add('save', 'submit');
	}

	/**
	 * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array(
			'data_class' => MailTemplateData::class,
			'attr' => array('novalidate' => 'novalidate'),
		));
	}

}
