<?php

namespace Shopsys\FrameworkBundle\Model\Customer;

use DateTime;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Model\Security\TimelimitLoginInterface;
use Shopsys\FrameworkBundle\Model\Security\UniqueLoginInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class FrontendUserProvider implements UserProviderInterface
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Customer\UserRepository
     */
    protected $userRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    protected $domain;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Customer\UserRepository $userRepository
     * @param \Shopsys\FrameworkBundle\Component\Domain\Domain $domain
     */
    public function __construct(UserRepository $userRepository, Domain $domain)
    {
        $this->userRepository = $userRepository;
        $this->domain = $domain;
    }

    /**
     * @param string $email
     * @return \Shopsys\FrameworkBundle\Model\Customer\User
     */
    public function loadUserByUsername($email)
    {
        $user = $this->userRepository->findUserByEmailAndDomain(mb_strtolower($email), $this->domain->getId());

        if ($user === null) {
            $message = sprintf(
                'Unable to find an active Shopsys\FrameworkBundle\Model\Customer\User object identified by email "%s".',
                $email
            );
            throw new \Symfony\Component\Security\Core\Exception\UsernameNotFoundException($message, 0);
        }

        return $user;
    }

    /**
     * @param \Symfony\Component\Security\Core\User\UserInterface $userInterface
     * @return \Shopsys\FrameworkBundle\Model\Customer\User
     */
    public function refreshUser(UserInterface $userInterface)
    {
        $class = get_class($userInterface);
        if (!$this->supportsClass($class)) {
            $message = sprintf('Instances of "%s" are not supported.', $class);
            throw new \Symfony\Component\Security\Core\Exception\UnsupportedUserException($message);
        }

        /** @var \Shopsys\FrameworkBundle\Model\Customer\User $user */
        $user = $userInterface;

        if ($user instanceof TimelimitLoginInterface) {
            if (time() - $user->getLastActivity()->getTimestamp() > 3600 * 24) {
                throw new \Symfony\Component\Security\Core\Exception\UsernameNotFoundException('User was too long unactive');
            }
            $user->setLastActivity(new DateTime());
        }

        if ($user instanceof UniqueLoginInterface) {
            $freshUser = $this->userRepository->findByIdAndLoginToken($user->getId(), $user->getLoginToken());
        } else {
            $freshUser = $this->userRepository->findById($user->getId());
        }

        if ($freshUser === null) {
            throw new \Symfony\Component\Security\Core\Exception\UsernameNotFoundException('Unable to find an active user');
        }

        return $freshUser;
    }

    /**
     * @param string $class
     * @return bool
     */
    public function supportsClass($class)
    {
        return $class === User::class || is_subclass_of($class, User::class);
    }
}
