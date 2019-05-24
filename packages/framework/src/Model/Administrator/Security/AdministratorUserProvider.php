<?php

namespace Shopsys\FrameworkBundle\Model\Administrator\Security;

use DateTime;
use Shopsys\FrameworkBundle\Model\Administrator\Activity\AdministratorActivityFacade;
use Shopsys\FrameworkBundle\Model\Administrator\Administrator;
use Shopsys\FrameworkBundle\Model\Administrator\AdministratorRepository;
use Shopsys\FrameworkBundle\Model\Security\TimelimitLoginInterface;
use Shopsys\FrameworkBundle\Model\Security\UniqueLoginInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class AdministratorUserProvider implements UserProviderInterface
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Administrator\AdministratorRepository
     */
    protected $administratorRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Administrator\Activity\AdministratorActivityFacade
     */
    protected $administratorActivityFacade;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Administrator\AdministratorRepository $administratorRepository
     * @param \Shopsys\FrameworkBundle\Model\Administrator\Activity\AdministratorActivityFacade $administratorActivityFacade
     */
    public function __construct(
        AdministratorRepository $administratorRepository,
        AdministratorActivityFacade $administratorActivityFacade
    ) {
        $this->administratorRepository = $administratorRepository;
        $this->administratorActivityFacade = $administratorActivityFacade;
    }

    /**
     * @param string $username The username
     * @return \Shopsys\FrameworkBundle\Model\Administrator\Administrator
     */
    public function loadUserByUsername($username)
    {
        $administrator = $this->administratorRepository->findByUserName($username);

        if ($administrator === null) {
            $message = sprintf(
                'Unable to find an active admin Shopsys\FrameworkBundle\Model\Administrator\Administrator object identified by "%s".',
                $username
            );
            throw new \Symfony\Component\Security\Core\Exception\UsernameNotFoundException($message, 0);
        }

        return $administrator;
    }

    /**
     * @param \Symfony\Component\Security\Core\User\UserInterface $userInterface
     * @return \Shopsys\FrameworkBundle\Model\Administrator\Administrator
     */
    public function refreshUser(UserInterface $userInterface)
    {
        $class = get_class($userInterface);
        if (!$this->supportsClass($class)) {
            $message = sprintf('Instances of "%s" are not supported.', $class);
            throw new \Symfony\Component\Security\Core\Exception\UnsupportedUserException($message);
        }

        /** @var \Shopsys\FrameworkBundle\Model\Administrator\Administrator $administrator */
        $administrator = $userInterface;

        $freshAdministrator = $this->administratorRepository->findById($administrator->getId());

        if ($administrator instanceof UniqueLoginInterface
            && $freshAdministrator !== null
            && $freshAdministrator->getLoginToken() !== $administrator->getLoginToken()
        ) {
            throw new \Symfony\Component\Security\Core\Exception\AuthenticationExpiredException();
        }

        if ($administrator instanceof TimelimitLoginInterface) {
            if (time() - $administrator->getLastActivity()->getTimestamp() > 3600 * 5) {
                throw new \Symfony\Component\Security\Core\Exception\AuthenticationExpiredException('Admin was too long inactive.');
            }
            if ($freshAdministrator !== null) {
                $freshAdministrator->setLastActivity(new DateTime());
            }
        }

        if ($freshAdministrator === null) {
            throw new \Symfony\Component\Security\Core\Exception\UsernameNotFoundException('Unable to find an active admin');
        }

        if ($freshAdministrator instanceof Administrator) {
            $this->administratorActivityFacade->updateCurrentActivityLastActionTime($freshAdministrator);
        }

        return $freshAdministrator;
    }

    /**
     * @param string $class
     * @return bool
     */
    public function supportsClass($class)
    {
        return Administrator::class === $class || is_subclass_of($class, Administrator::class);
    }
}
