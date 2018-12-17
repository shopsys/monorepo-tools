<?php

namespace Shopsys\FrameworkBundle\Model\Customer;

use Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class UserFactory implements UserFactoryInterface
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver
     */
    protected $entityNameResolver;

    /**
     * @var \Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface
     */
    protected $encoderFactory;

    /**
     * @param \Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver $entityNameResolver
     * @param \Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface $encoderFactory
     */
    public function __construct(EntityNameResolver $entityNameResolver, EncoderFactoryInterface $encoderFactory)
    {
        $this->entityNameResolver = $entityNameResolver;
        $this->encoderFactory = $encoderFactory;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Customer\UserData $userData
     * @param \Shopsys\FrameworkBundle\Model\Customer\BillingAddress $billingAddress
     * @param \Shopsys\FrameworkBundle\Model\Customer\DeliveryAddress|null $deliveryAddress
     * @param \Shopsys\FrameworkBundle\Model\Customer\User|null $userByEmail
     * @return \Shopsys\FrameworkBundle\Model\Customer\User
     */
    public function create(
        UserData $userData,
        BillingAddress $billingAddress,
        ?DeliveryAddress $deliveryAddress,
        ?User $userByEmail
    ): User {
        if ($userByEmail instanceof User) {
            $isSameEmail = (mb_strtolower($userByEmail->getEmail()) === mb_strtolower($userData->email));
            $isSameDomain = ($userByEmail->getDomainId() === $userData->domainId);
            if ($isSameEmail && $isSameDomain) {
                throw new \Shopsys\FrameworkBundle\Model\Customer\Exception\DuplicateEmailException($userData->email);
            }
        }

        $classData = $this->entityNameResolver->resolve(User::class);

        $user = new $classData($userData, $billingAddress, $deliveryAddress);

        $user->changePassword($this->encoderFactory, $userData->password);

        return $user;
    }
}
