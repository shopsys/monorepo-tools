<?php

namespace SS6\ShopBundle\Component\Constraints;

use SS6\ShopBundle\Component\Constraints\UniqueSlugsOnDomains;
use SS6\ShopBundle\Component\Router\DomainRouterFactory;
use SS6\ShopBundle\Component\Translation\Translator;
use SS6\ShopBundle\Model\Domain\Domain;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueSlugsOnDomainsValidator extends ConstraintValidator {

	/**
	 * @var \SS6\ShopBundle\Model\Domain\Domain
	 */
	private $domain;

	/**
	 * @var \SS6\ShopBundle\Component\Router\DomainRouterFactory
	 */
	private $domainRouterFactory;

	/**
	 * @var \SS6\ShopBundle\Component\Translation\Translator
	 */
	private $translator;

	public function __construct(Domain $domain, DomainRouterFactory $domainRouterFactory, Translator $translator) {
		$this->domain = $domain;
		$this->domainRouterFactory = $domainRouterFactory;
		$this->translator = $translator;
	}

	/**
	 * @param array $values
	 * @param \Symfony\Component\Validator\Constraint $constraint
	 */
	public function validate($values, Constraint $constraint) {
		if (!$constraint instanceof UniqueSlugsOnDomains) {
			throw new \Symfony\Component\Validator\Exception\UnexpectedTypeException($constraint, UniqueSlugsOnDomains::class);
		}

		foreach ($values as $domainId => $slugs) {
			foreach ($slugs as $slug) {
				$domainRouter = $this->domainRouterFactory->getRouter($domainId);
				try {
					$domainRouter->match('/' . $slug);
				} catch (\Symfony\Component\Routing\Exception\ResourceNotFoundException $e) {
					continue;
				}
				$this->context->addViolation($this->translator->trans(
					'Adresa %%url%% již existuje.',
					[
						'%%url%%' => $this->domain->getDomainConfigById($domainId)->getUrl() . '/' . $slug,
					]
				));
			}
		}
	}

}
