<?php

namespace SS6\ShopBundle\Form\Admin\Order\Status;

use SS6\ShopBundle\Form\Admin\Mail\MailTemplateFormType;
use SS6\ShopBundle\Form\FormType;
use SS6\ShopBundle\Model\Mail\AllMailTemplatesData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AllMailTemplatesFormType extends AbstractType {

	/**
	 * @return string
	 */
	public function getName() {
		return 'all_mail_templates';
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('registrationTemplate', new MailTemplateFormType())
			->add('resetPasswordTemplate', new MailTemplateFormType())
			->add('orderStatusTemplates', FormType::COLLECTION, [
				'type' => new MailTemplateFormType(),
			])
			->add('domainId', FormType::HIDDEN);
	}

	/**
	 * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults([
			'attr' => ['novalidate' => 'novalidate'],
			'data_class' => AllMailTemplatesData::class,
		]);
	}

}
