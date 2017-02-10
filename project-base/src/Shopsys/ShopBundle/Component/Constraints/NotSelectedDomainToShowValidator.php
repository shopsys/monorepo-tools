<?php

namespace Shopsys\ShopBundle\Component\Constraints;

use Shopsys\ShopBundle\Component\Domain\Domain;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class NotSelectedDomainToShowValidator extends ConstraintValidator {

	/**
	 * @var \Shopsys\ShopBundle\Component\Domain\Domain
	 */
	private $domain;

	public function __construct(Domain $domain) {
		$this->domain = $domain;
	}

	/**
	 * @param array $values
	 * @param \Symfony\Component\Validator\Constraint $constraint
	 */
	public function validate($values, Constraint $constraint) {
		if (!$constraint instanceof NotSelectedDomainToShow) {
			throw new \Symfony\Component\Validator\Exception\UnexpectedTypeException($constraint, NotSelectedDomainToShow::class);
		}

		$allDomains = $this->domain->getAll();

		if (count($allDomains) === count($values)) {
			$this->context->addViolation($constraint->message);
			return;
		}
	}
}
