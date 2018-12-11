<?php

namespace Shopsys\FrameworkBundle\Model\Customer;

use Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver;

class UserFactory implements UserFactoryInterface
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver
     */
    protected $entityNameResolver;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Customer\CustomerPasswordService
     */
    protected $customerPasswordService;

    /**
     * @param \Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver $entityNameResolver
     * @param \Shopsys\FrameworkBundle\Model\Customer\CustomerPasswordService $customerPasswordService
     */
    public function __construct(EntityNameResolver $entityNameResolver, CustomerPasswordService $customerPasswordService)
    {
        $this->entityNameResolver = $entityNameResolver;
        $this->customerPasswordService = $customerPasswordService;
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

        $this->customerPasswordService->changePassword($user, $userData->password);

        return $user;
    }
}
