<?php

namespace SS6\ShopBundle\Form\Front\Contact;

use SS6\ShopBundle\Form\FormType;
use SS6\ShopBundle\Form\TimedFormTypeExtension;
use SS6\ShopBundle\Model\ContactForm\ContactFormData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class ContactFormType extends AbstractType {

	/**
	 * @return string
	 */
	public function getName() {
		return 'contact_form';
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('name', FormType::TEXT, [
				'required' => true,
				'constraints' => [
					new Constraints\NotBlank(['message' => 'Vyplňte prosím jméno']),
				],
			])
			->add('message', FormType::TEXTAREA, [
				'required' => true,
				'constraints' => [
					new Constraints\NotBlank(['message' => 'Vyplňte prosím text']),
				],
			])
			->add('email', FormType::EMAIL, [
				'constraints' => [
					new Constraints\NotBlank(['message' => 'Vyplňte prosím e-mail']),
					new Constraints\Email(['message' => 'Vyplňte prosím platný e-mail']),
				],
			])
			->add('email2', FormType::HONEY_POT)
			->add('send', FormType::SUBMIT);
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults([
			'data_class' => ContactFormData::class,
			'attr' => ['novalidate' => 'novalidate'],
			TimedFormTypeExtension::OPTION_ENABLED => true,
		]);
	}
}
