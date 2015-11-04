<?php

namespace SS6\ShopBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;

class FriendlyUrlType extends AbstractType {

	const SLUG_REGEX = '/^[\w_\-\/]+$/';

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder->add('domain', FormType::DOMAIN, [
			'displayUrl' => true,
			'required' => true,
		]);
		$builder->add('slug', FormType::TEXT, [
			'required' => true,
			'constraints' => [
				new Constraints\NotBlank(),
				new Constraints\Regex(self::SLUG_REGEX),
			],
		]);
	}

	/**
	 * @inheritdoc
	 */
	public function getParent() {
		return FormType::FORM;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return FormType::FRIENDLY_URL;
	}

}
