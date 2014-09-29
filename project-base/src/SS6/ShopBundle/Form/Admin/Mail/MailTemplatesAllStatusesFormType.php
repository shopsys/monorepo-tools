<?php

namespace SS6\ShopBundle\Form\Admin\Mail;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MailTemplatesAllStatusesFormType extends AbstractType{

	/**
	 * @var array
	 */
	private $mailTemplateNames;

	public function __construct($mailTemplateNames) {
		$this->mailTemplateNames = $mailTemplateNames;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'mail_templates_all_statuses';
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		foreach ($this->mailTemplateNames as $mailTemplateName) {
			$builder->add($mailTemplateName, new MailTemplateFormType());
		}
	}

	/**
	 * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array(
			'attr' => array('novalidate' => 'novalidate'),
		));
	}

}
