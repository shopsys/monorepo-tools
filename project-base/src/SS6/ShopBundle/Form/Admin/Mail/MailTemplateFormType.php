<?php

namespace SS6\ShopBundle\Form\Admin\Mail;

use SS6\ShopBundle\Component\Transformers\EmptyWysiwygTransformer;
use SS6\ShopBundle\Model\Mail\MailTemplateData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class MailTemplateFormType extends AbstractType {

	const VALIDATION_GROUP_SEND_MAIL = 'sendMail';

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
			->add('subject', 'text', [
				'required' => true,
				'constraints' => [
					new Constraints\NotBlank([
						'message' => 'Vyplňte prosím předmět',
						'groups' => [self::VALIDATION_GROUP_SEND_MAIL],
					]),
				],
			])
			->add(
				$builder
					->create('body', 'ckeditor', [
						'required' => true,
						'constraints' => [
							new Constraints\NotBlank([
								'message' => 'Vyplňte prosím text emailu',
								'groups' => [self::VALIDATION_GROUP_SEND_MAIL],
							]),
						],
					])
					->addModelTransformer(new EmptyWysiwygTransformer())
			)
			->add('sendMail', 'checkbox', ['required' => false])
			->add('save', 'submit');
	}

	/**
	 * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults([
			'data_class' => MailTemplateData::class,
			'attr' => ['novalidate' => 'novalidate'],
			'validation_groups' => function (FormInterface $form) {
				$validationGroups = ['Default'];

				$mailTemplateData = $form->getData();
				/* @var $mailTemplateData \SS6\ShopBundle\Model\Mail\MailTemplateData */

				if ($mailTemplateData->sendMail) {
					$validationGroups[] = self::VALIDATION_GROUP_SEND_MAIL;
				}

				return $validationGroups;
			},
		]);
	}

}
