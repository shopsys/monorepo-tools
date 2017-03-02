<?php

namespace Shopsys\ShopBundle\Model\Administrator;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;

class AdministratorService
{
    /**
     * @var \Symfony\Component\Security\Core\Encoder\EncoderFactory
     */
    private $encoderFactory;

    /**
     * @var \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage
     */
    private $tokenStorage;

    public function __construct(
        EncoderFactory $encoderFactory,
        TokenStorage $tokenStorage
    ) {
        $this->encoderFactory = $encoderFactory;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Administrator\Administrator $administrator
     * @param string $password
     * @return string
     */
    public function getPasswordHash(Administrator $administrator, $password)
    {
        $encoder = $this->encoderFactory->getEncoder($administrator);
        $passwordHash = $encoder->encodePassword($password, $administrator->getSalt());

        return $passwordHash;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Administrator\Administrator $administrator
     * @param int $adminCountExcludingSuperadmin
     */
    public function delete(Administrator $administrator, $adminCountExcludingSuperadmin)
    {
        if ($adminCountExcludingSuperadmin === 1) {
            throw new \Shopsys\ShopBundle\Model\Administrator\Exception\DeletingLastAdministratorException();
        }
        if ($this->tokenStorage->getToken()->getUser() === $administrator) {
            throw new \Shopsys\ShopBundle\Model\Administrator\Exception\DeletingSelfException();
        }
        if ($administrator->isSuperadmin()) {
            throw new \Shopsys\ShopBundle\Model\Administrator\Exception\DeletingSuperadminException();
        }
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Administrator\AdministratorData $administratorData
     * @param \Shopsys\ShopBundle\Model\Administrator\Administrator $administrator
     * @param string[] $superadminUsernames
     * @param \Shopsys\ShopBundle\Model\Administrator\Administrator|null $administratorByUserName
     * @return \Shopsys\ShopBundle\Model\Administrator\Administrator
     */
    public function edit(
        AdministratorData $administratorData,
        Administrator $administrator,
        array $superadminUsernames,
        Administrator $administratorByUserName = null
    ) {
        if (in_array($administratorData->username, $superadminUsernames, true)) {
            throw new \Shopsys\ShopBundle\Model\Administrator\Exception\DuplicateSuperadminNameException($administratorData->username);
        }
        if ($administratorByUserName !== null
            && $administratorByUserName !== $administrator
            && $administratorByUserName->getUsername() === $administratorData->username
        ) {
            throw new \Shopsys\ShopBundle\Model\Administrator\Exception\DuplicateUserNameException($administrator->getUsername());
        }
        if ($administrator->isSuperadmin()) {
            throw new \Shopsys\ShopBundle\Model\Administrator\Exception\EditingSuperadminException();
        }
        $administrator->edit($administratorData);
        if ($administratorData->password !== null) {
            $administrator->setPassword($this->getPasswordHash($administrator, $administratorData->password));
        }

        return $administrator;
    }
}
