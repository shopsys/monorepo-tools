<?php

namespace SS6\ShopBundle\Form\Admin\Mail;

use SS6\ShopBundle\Component\Transformers\EmptyWysiwygTransformer;
use SS6\ShopBundle\Model\Mail\MailTemplateData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
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
				'required' => true,
				'constraints' => array(
					new Constraints\NotBlank(array(
						'message' => 'Vyplňte prosím předmět',
						'groups' => array('sendMail'),
					))
				)
			))
			->add(
				$builder
					->create('body', 'ckeditor', array(
						'required' => true,
						'constraints' => array(
							new Constraints\NotBlank(array(
								'message' => 'Vyplňte prosím text emailu',
								'groups' => array('sendMail'),
							))
						)
					))
					->addModelTransformer(new EmptyWysiwygTransformer())
			)
			->add('sendMail', 'checkbox', array('required' => false))
			->add('save', 'submit');
	}

	/**
	 * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array(
			'data_class' => MailTemplateData::class,
			'attr' => array('novalidate' => 'novalidate'),
			'validation_groups' => function(FormInterface $form) {
				$validationGroups = array('Default');

				$mailTemplateData = $form->getData();
				/* @var $mailTemplateData \SS6\ShopBundle\Model\Mail\MailTemplateData */

				if ($mailTemplateData->sendMail) {
					$validationGroups[] = 'sendMail';
				}

				return $validationGroups;
			}
		));
	}

}
