<?php

namespace SS6\ShopBundle\Form\Admin\Mail;

use SS6\ShopBundle\Component\Constraints\Contains;
use SS6\ShopBundle\Component\Constraints\Email;
use SS6\ShopBundle\Component\Transformers\EmptyWysiwygTransformer;
use SS6\ShopBundle\Form\FormType;
use SS6\ShopBundle\Form\ValidationGroup;
use SS6\ShopBundle\Model\Mail\MailTemplateData;
use SS6\ShopBundle\Model\Mail\MailTypeInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class MailTemplateFormType extends AbstractType {

	const VALIDATION_GROUP_SEND_MAIL = 'sendMail';

	/**
	 * @var \SS6\ShopBundle\Model\Mail\MailTypeInterface
	 */
	private $mailType;

	/**
	 * @param \SS6\ShopBundle\Model\Mail\MailTypeInterface $mailType
	 */
	public function __construct(MailTypeInterface $mailType) {
		$this->mailType = $mailType;
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('bccEmail', FormType::EMAIL, [
				'required' => false,
				'constraints' => [
					new Email(),
					new Constraints\Length(['max' => 255, 'maxMessage' => 'E-mail nesmí být delší než {{ limit }} znaků']),
				],
			])
			->add('subject', FormType::TEXT, [
				'required' => true,
				'constraints' => $this->getSubjectConstraints(),
			])
			->add(
				$builder
					->create('body', FormType::WYSIWYG, [
						'required' => true,
						'config_name' => 'email',
						'constraints' => $this->getBodyConstraints(),
					])
					->addModelTransformer(new EmptyWysiwygTransformer())
			)
			->add('attachment', FormType::FILE_UPLOAD, [
				'required' => false,
				'multiple' => false,
				'file_constraints' => [
					new Constraints\File([
						'maxSize' => '2M',
						'maxSizeMessage' => 'Nahraný soubor je příliš velký ({{ size }} {{ suffix }}). '
							. 'Maximální velikost souboru je {{ limit }} {{ suffix }}.',
					]),
				],
			])
			->add('deleteAttachment', FormType::CHECKBOX)
			->add('sendMail', FormType::CHECKBOX, ['required' => false]);
	}

	/**
	 * @return \Symfony\Component\Validator\Constraint[]
	 */
	private function getSubjectConstraints() {
		$subjectConstraints = [];

		$subjectConstraints[] = new Constraints\NotBlank([
			'message' => 'Vyplňte prosím předmět',
			'groups' => [self::VALIDATION_GROUP_SEND_MAIL],
		]);
		$subjectConstraints[] = new Constraints\Length([
			'max' => 255,
			'maxMessage' => 'Předmět e-mailu nesmí být delší než {{ limit }} znaků',
		]);

		foreach ($this->mailType->getRequiredSubjectVariables() as $variableName) {
			$subjectConstraints[] = new Contains([
				'needle' => $variableName,
				'message' => 'Proměnná {{ needle }} je povinná',
				'groups' => [self::VALIDATION_GROUP_SEND_MAIL],
			]);
		}

		return $subjectConstraints;
	}

	/**
	 * @return \Symfony\Component\Validator\Constraint[]
	 */
	private function getBodyConstraints() {
		$bodyConstraints = [];

		$bodyConstraints[] = new Constraints\NotBlank([
			'message' => 'Vyplňte prosím text emailu',
			'groups' => [self::VALIDATION_GROUP_SEND_MAIL],
		]);

		foreach ($this->mailType->getRequiredBodyVariables() as $variableName) {
			$bodyConstraints[] = new Contains([
				'needle' => $variableName,
				'message' => 'Proměnná {{ needle }} je povinná',
				'groups' => [self::VALIDATION_GROUP_SEND_MAIL],
			]);
		}

		return $bodyConstraints;
	}

	/**
	 * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults([
			'data_class' => MailTemplateData::class,
			'attr' => ['novalidate' => 'novalidate'],
			'validation_groups' => function (FormInterface $form) {
				$validationGroups = [ValidationGroup::VALIDATION_GROUP_DEFAULT];

				$mailTemplateData = $form->getData();
				/* @var $mailTemplateData \SS6\ShopBundle\Model\Mail\MailTemplateData */

				if ($mailTemplateData->sendMail) {
					$validationGroups[] = self::VALIDATION_GROUP_SEND_MAIL;
				}

				return $validationGroups;
			},
		]);
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'mail_template_form';
	}

}
