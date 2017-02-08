<?php

namespace Shopsys\ShopBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;

class FriendlyUrlType extends AbstractType {

	const FIELD_DOMAIN = 'domain';
	const FIELD_SLUG = 'slug';

	const SLUG_REGEX = '/^[\w_\-\/]+$/';

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder->add(self::FIELD_DOMAIN, FormType::DOMAIN, [
			'displayUrl' => true,
			'required' => true,
		]);
		$builder->add(self::FIELD_SLUG, FormType::TEXT, [
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
