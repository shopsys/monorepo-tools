<?php

namespace SS6\CoreBundle\Model\Administrator\Repository;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

class AdministratorRepository extends EntityRepository implements UserProviderInterface {

	/**
	 * @param string $username The username
	 * @return SS6\CoreBundle\Model\Administrator\Entity\Administrator
	 * @throws UsernameNotFoundException if the user is not found
	 */
	public function loadUserByUsername($username) {
		$administrator = $this->findOneBy(array('username' => $username));

		if ($administrator === null) {
			$message = sprintf(
				'Unable to find an active admin SS6\CoreBundle\Model\Administrator\Entity\Administrator object identified by "%s".', $username
			);
			throw new UsernameNotFoundException($message, 0);
		}

		return $administrator;
	}

	/**
	 * @param Symfony\Component\Security\Core\User\UserInterface $administrator
	 * @return SS6\CoreBundle\Model\Administrator\Entity\Administrator
	 * @throws UnsupportedUserException
	 */
	public function refreshUser(UserInterface $administrator) {
		$class = get_class($administrator);
		if (!$this->supportsClass($class)) {
			throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $class));
		}

		return $this->find($administrator->getId());
	}

	/**
	 * @param string $class
	 * @return bool
	 */
	public function supportsClass($class) {
		return $this->getEntityName() === $class || is_subclass_of($class, $this->getEntityName());
	}

}
