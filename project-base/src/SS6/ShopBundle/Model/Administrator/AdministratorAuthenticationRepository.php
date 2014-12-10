<?php

namespace SS6\ShopBundle\Model\Administrator;

use DateTime;
use Doctrine\ORM\EntityRepository;
use SS6\ShopBundle\Model\Security\UniqueLoginInterface;
use SS6\ShopBundle\Model\Security\TimelimitLoginInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class AdministratorAuthenticationRepository extends EntityRepository implements UserProviderInterface {

	/**
	 * @param string $username The username
	 * @return \SS6\ShopBundle\Model\Administrator\Administrator
	 */
	public function loadUserByUsername($username) {
		$administrator = $this->findOneBy(array('username' => $username));

		if ($administrator === null) {
			$message = sprintf(
				'Unable to find an active admin SS6\ShopBundle\Model\Administrator\Administrator object identified by "%s".', $username
			);
			throw new \Symfony\Component\Security\Core\Exception\UsernameNotFoundException($message, 0);
		}

		return $administrator;
	}

	/**
	 * @param \Symfony\Component\Security\Core\User\UserInterface $administrator
	 * @return \SS6\ShopBundle\Model\Administrator\Administrator
	 */
	public function refreshUser(UserInterface $administrator) {
		$class = get_class($administrator);
		if (!$this->supportsClass($class)) {
			$message = sprintf('Instances of "%s" are not supported.', $class);
			throw new \Symfony\Component\Security\Core\Exception\UnsupportedUserException($message);
		}

		if ($administrator instanceof TimelimitLoginInterface) {
			if (time() - $administrator->getLastActivity()->getTimestamp() > 3600 * 5) {
				throw new \Symfony\Component\Security\Core\Exception\UsernameNotFoundException('Admin was too long unactive.');
			}
			$administrator->setLastActivity(new DateTime());
		}

		$findParams = array(
			'id' => $administrator->getId(),
		);
		if ($administrator instanceof UniqueLoginInterface) {
			$findParams['loginToken'] = $administrator->getLoginToken();
		}
		$freshAdministrator = $this->findOneBy($findParams);

		if ($freshAdministrator === null) {
			throw new \Symfony\Component\Security\Core\Exception\UsernameNotFoundException('Unable to find an active admin');
		}

		return $freshAdministrator;
	}

	/**
	 * @param string $class
	 * @return bool
	 */
	public function supportsClass($class) {
		return $this->getEntityName() === $class || is_subclass_of($class, $this->getEntityName());
	}

}
