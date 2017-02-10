<?php

namespace Shopsys\ShopBundle\Model\Administrator\Security;

use DateTime;
use Shopsys\ShopBundle\Model\Administrator\Activity\AdministratorActivityFacade;
use Shopsys\ShopBundle\Model\Administrator\Administrator;
use Shopsys\ShopBundle\Model\Administrator\AdministratorRepository;
use Shopsys\ShopBundle\Model\Security\TimelimitLoginInterface;
use Shopsys\ShopBundle\Model\Security\UniqueLoginInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class AdministratorUserProvider implements UserProviderInterface
{

    /**
     * @var \Shopsys\ShopBundle\Model\Administrator\AdministratorRepository
     */
    private $administratorRepository;

    /**
     * @var \Shopsys\ShopBundle\Model\Administrator\Activity\AdministratorActivityFacade
     */
    private $administratorActivityFacade;

    public function __construct(
        AdministratorRepository $administratorRepository,
        AdministratorActivityFacade $administratorActivityFacade
    ) {
        $this->administratorRepository = $administratorRepository;
        $this->administratorActivityFacade = $administratorActivityFacade;
    }

    /**
     * @param string $username The username
     * @return \Shopsys\ShopBundle\Model\Administrator\Administrator
     */
    public function loadUserByUsername($username) {
        $administrator = $this->administratorRepository->findByUserName($username);

        if ($administrator === null) {
            $message = sprintf(
                'Unable to find an active admin Shopsys\ShopBundle\Model\Administrator\Administrator object identified by "%s".', $username
            );
            throw new \Symfony\Component\Security\Core\Exception\UsernameNotFoundException($message, 0);
        }

        return $administrator;
    }

    /**
     * @param \Symfony\Component\Security\Core\User\UserInterface $administrator
     * @return \Shopsys\ShopBundle\Model\Administrator\Administrator
     */
    public function refreshUser(UserInterface $administrator) {
        $class = get_class($administrator);
        if (!$this->supportsClass($class)) {
            $message = sprintf('Instances of "%s" are not supported.', $class);
            throw new \Symfony\Component\Security\Core\Exception\UnsupportedUserException($message);
        }

        if ($administrator instanceof UniqueLoginInterface) {
            $freshAdministrator = $this->administratorRepository->findByIdAndLoginToken(
                $administrator->getId(),
                $administrator->getLoginToken()
            );
        } else {
            $freshAdministrator = $this->administratorRepository->findById($administrator->getId());
        }

        if ($administrator instanceof TimelimitLoginInterface) {
            if (time() - $administrator->getLastActivity()->getTimestamp() > 3600 * 5) {
                throw new \Symfony\Component\Security\Core\Exception\UsernameNotFoundException('Admin was too long unactive.');
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
    public function supportsClass($class) {
        return Administrator::class === $class || is_subclass_of($class, Administrator::class);
    }

}
