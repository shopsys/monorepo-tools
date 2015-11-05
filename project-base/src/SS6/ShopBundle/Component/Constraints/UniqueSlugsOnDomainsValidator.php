<?php

namespace SS6\ShopBundle\Component\Constraints;

use SS6\ShopBundle\Component\Constraints\UniqueSlugsOnDomains;
use SS6\ShopBundle\Component\Domain\Config\DomainConfig;
use SS6\ShopBundle\Component\Domain\Domain;
use SS6\ShopBundle\Component\Router\DomainRouterFactory;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueSlugsOnDomainsValidator extends ConstraintValidator {

	/**
	 * @var \SS6\ShopBundle\Component\Domain\Domain
	 */
	private $domain;

	/**
	 * @var \SS6\ShopBundle\Component\Router\DomainRouterFactory
	 */
	private $domainRouterFactory;

	public function __construct(Domain $domain, DomainRouterFactory $domainRouterFactory) {
		$this->domain = $domain;
		$this->domainRouterFactory = $domainRouterFactory;
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
			$domainConfig = $this->domain->getDomainConfigById($domainId);

			$this->validateDuplication($domainConfig, $slugs);
			$this->validateExists($domainConfig, $slugs);
		}
	}

	/**
	 * @param \SS6\ShopBundle\Component\Domain\Config\DomainConfig $domainConfig
	 * @param string[] $slugs
	 */
	private function validateDuplication(DomainConfig $domainConfig, $slugs) {
		foreach (array_count_values($slugs) as $slug => $count) {
			if ($count > 1) {
				$this->context->addViolation(t(
					'Adresa %%url%% může být zadána pouze jednou.',
					[
						'%%url%%' => $domainConfig->getUrl() . '/' . $slug,
					]
				));
			}
		}
	}

	/**
	 * @param \SS6\ShopBundle\Component\Domain\Config\DomainConfig $domainConfig
	 * @param string[] $slugs
	 */
	private function validateExists(DomainConfig $domainConfig, $slugs) {
		foreach ($slugs as $slug) {
			$domainRouter = $this->domainRouterFactory->getRouter($domainConfig->getId());
			try {
				$domainRouter->match('/' . $slug);
			} catch (\Symfony\Component\Routing\Exception\ResourceNotFoundException $e) {
				continue;
			}
			$this->context->addViolation(t(
				'Adresa %%url%% již existuje.',
				[
					'%%url%%' => $domainConfig->getUrl() . '/' . $slug,
				]
			));
		}
	}

}
