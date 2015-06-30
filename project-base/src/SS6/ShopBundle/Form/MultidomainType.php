<?php

namespace SS6\ShopBundle\Form;

use SS6\ShopBundle\Component\Condition;
use SS6\ShopBundle\Model\Domain\Domain;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MultidomainType extends AbstractType {

	/**
	 * @var \SS6\ShopBundle\Model\Domain\Domain
	 */
	private $domain;

	/**
	 * @param \SS6\ShopBundle\Model\Domain\Domain $domain
	 */
	public function __construct(Domain $domain) {
		$this->domain = $domain;
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		Condition::setArrayDefaultValue($options['options'], 'required', $options['required']);
		Condition::setArrayDefaultValue($options['options'], 'constraints', []);

		$otherLocaleOptions = $options['options'];
		$otherLocaleOptions['required'] = $options['required'] && $otherLocaleOptions['required'];

		foreach ($this->domain->getAll() as $domainConfig) {
			$builder->add($domainConfig->getId(), $options['type'], $otherLocaleOptions);
		}
	}

	/**
	 * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults([
			'compound' => true,
			'options' => [],
			'type' => 'text',
		]);
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'multidomain';
	}

}
