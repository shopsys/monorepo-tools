<?php

namespace Shopsys\FrameworkBundle\Form\Constraints;

use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Model\Customer\CustomerFacade;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueEmailValidator extends ConstraintValidator
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Customer\CustomerFacade
     */
    private $customerFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    private $domain;

    public function __construct(
        CustomerFacade $customerFacade,
        Domain $domain
    ) {
        $this->customerFacade = $customerFacade;
        $this->domain = $domain;
    }

    /**
     * @param mixed $value
     * @param \Symfony\Component\Validator\Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof UniqueEmail) {
            throw new \Symfony\Component\Validator\Exception\UnexpectedTypeException($constraint, UniqueCollection::class);
        }

        $email = (string)$value;
        $domainId = $this->domain->getId();

        if ($constraint->ignoredEmail != $value && $this->customerFacade->findUserByEmailAndDomain($email, $domainId) !== null) {
            $this->context->addViolation(
                $constraint->message,
                [
                    '{{ email }}' => $email,
                ]
            );
        }
    }
}
