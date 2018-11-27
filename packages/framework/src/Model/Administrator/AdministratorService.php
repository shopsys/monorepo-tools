<?php

namespace Shopsys\FrameworkBundle\Model\Administrator;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AdministratorService
{
    /**
     * @var \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @param \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface $tokenStorage
     */
    public function __construct(
        TokenStorageInterface $tokenStorage
    ) {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Administrator\Administrator $administrator
     * @param int $adminCountExcludingSuperadmin
     */
    public function delete(Administrator $administrator, $adminCountExcludingSuperadmin)
    {
        if ($adminCountExcludingSuperadmin === 1) {
            throw new \Shopsys\FrameworkBundle\Model\Administrator\Exception\DeletingLastAdministratorException();
        }
        if ($this->tokenStorage->getToken()->getUser() === $administrator) {
            throw new \Shopsys\FrameworkBundle\Model\Administrator\Exception\DeletingSelfException();
        }
        if ($administrator->isSuperadmin()) {
            throw new \Shopsys\FrameworkBundle\Model\Administrator\Exception\DeletingSuperadminException();
        }
    }
}
