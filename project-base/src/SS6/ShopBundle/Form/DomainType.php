<?php

namespace SS6\ShopBundle\Form;

use SS6\ShopBundle\Component\Domain\Domain;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DomainType extends AbstractType {

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
	 * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$choices = [];
		foreach ($this->domain->getAll() as $domainConfig) {
			$choices[$domainConfig->getId()] = $domainConfig->getName();
		}

		$resolver->setDefaults([
			'choices' => $choices,
			'multiple' => false,
			'expanded' => false,
		]);
	}

	/**
	 * @return string
	 */
	public function getParent() {
		return 'choice';
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'domain';
	}

}
