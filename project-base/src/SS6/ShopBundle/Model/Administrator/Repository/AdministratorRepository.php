<?php

namespace SS6\ShopBundle\Model\Administrator\Repository;

use DateTime;
use Doctrine\ORM\EntityRepository;
use SS6\ShopBundle\Model\Administrator\Entity\Administrator;
use SS6\ShopBundle\Model\Security\UniqueLoginInterface;
use SS6\ShopBundle\Model\Security\TimelimitLoginInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class AdministratorRepository extends EntityRepository implements UserProviderInterface {

	/**
	 * @param string $username The username
	 * @return Administrator
	 * @throws UsernameNotFoundException if the user is not found
	 */
	public function loadUserByUsername($username) {
		$administrator = $this->findOneBy(array('username' => $username));

		if ($administrator === null) {
			$message = sprintf(
				'Unable to find an active admin SS6\ShopBundle\Model\Administrator\Entity\Administrator object identified by "%s".', $username
			);
			throw new UsernameNotFoundException($message, 0);
		}

		return $administrator;
	}

	/**
	 * @param UserInterface $administrator
	 * @return Administrator
	 * @throws UnsupportedUserException
	 */
	public function refreshUser(UserInterface $administrator) {
		$class = get_class($administrator);
		if (!$this->supportsClass($class)) {
			throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $class));
		}
		
		if ($administrator instanceof TimelimitLoginInterface) {
			if (time() - $administrator->getLastActivity()->getTimestamp() > 3600 * 5) {
				throw new UsernameNotFoundException('Admin was too long unactive.');
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
			throw new UsernameNotFoundException('Unable to find an active admin');
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
