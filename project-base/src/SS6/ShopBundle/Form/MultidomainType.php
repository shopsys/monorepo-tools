<?php

namespace SS6\ShopBundle\Form;

use SS6\ShopBundle\Component\Condition;
use SS6\ShopBundle\Component\Domain\Domain;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MultidomainType extends AbstractType {

	/**
	 * @var \SS6\ShopBundle\Component\Domain\Domain
	 */
	private $domain;

	/**
	 * @param \SS6\ShopBundle\Component\Domain\Domain $domain
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

		$subOptions = $options['options'];
		$subOptions['required'] = $options['required'] && $subOptions['required'];

		foreach ($this->domain->getAll() as $domainConfig) {
			if (array_key_exists($domainConfig->getId(), $options['optionsByDomainId'])) {
				$domainOptions = array_merge($subOptions, $options['optionsByDomainId'][$domainConfig->getId()]);
			} else {
				$domainOptions = $subOptions;
			}

			$builder->add($domainConfig->getId(), $options['type'], $domainOptions);
		}
	}

	/**
	 * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults([
			'compound' => true,
			'options' => [],
			'optionsByDomainId' => [],
			'type' => 'text',
		]);
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'multidomain';
	}

	/**
	 * @param \Symfony\Component\Form\FormView $view
	 * @param \Symfony\Component\Form\FormInterface $form
	 * @param array $options
	 */
	public function finishView(FormView $view, FormInterface $form, array $options) {
		parent::finishView($view, $form, $options);

		foreach ($view->children as $domainId => $child) {
			$child->vars['domainConfig'] = $this->domain->getDomainConfigById($domainId);
		}
	}

}
