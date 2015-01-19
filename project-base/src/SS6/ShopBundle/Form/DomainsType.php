<?php

namespace SS6\ShopBundle\Form;

use SS6\ShopBundle\Model\Domain\Domain;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DomainsType extends AbstractType {

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
	 * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$choices = [];
		foreach ($this->domain->getAll() as $domainConfig) {
			$choices[$domainConfig->getId()] = $domainConfig->getDomain();
		}

		$resolver->setDefaults([
			'choices' => $choices,
			'multiple' => true,
			'expanded' => true,
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
		return 'domains';
	}

}
