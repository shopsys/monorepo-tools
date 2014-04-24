<?php

namespace SS6\ShopBundle\Model\Customer;

use DateTime;
use Doctrine\ORM\EntityRepository;
use SS6\ShopBundle\Model\Customer\UserIdentity;
use SS6\ShopBundle\Model\Security\UniqueLoginInterface;
use SS6\ShopBundle\Model\Security\TimelimitLoginInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class SecurityUserIdentityRepository extends EntityRepository implements UserProviderInterface {

	/**
	 * @return \Doctrine\ORM\EntityRepository
	 */
	protected function getUserIdentityRepository() {
		return $this->em->getRepository(UserIdentity::class);
	}
	

	/**
	 * @param string $email
	 * @return Administrator
	 * @throws UsernameNotFoundException if the user is not found
	 */
	public function loadUserByUsername($email) {
		$userIdentity = $this->findOneBy(array('email' => $email));

		if ($userIdentity === null) {
			$message = sprintf(
				'Unable to find an active SS6\ShopBundle\Model\Customer\UserIdentity object identified by email "%s".', $email
			);
			throw new \Symfony\Component\Security\Core\Exception\UsernameNotFoundException($message, 0);
		}

		return $userIdentity;
	}

	/**
	 * @param UserInterface $userIdentity
	 * @return Administrator
	 * @throws UnsupportedUserException
	 */
	public function refreshUser(UserInterface $userIdentity) {
		$class = get_class($userIdentity);
		if (!$this->supportsClass($class)) {
			$message = sprintf('Instances of "%s" are not supported.', $class);
			throw new \Symfony\Component\Security\Core\Exception\UnsupportedUserException($message);
		}
		
		if ($userIdentity instanceof TimelimitLoginInterface) {
			if (time() - $userIdentity->getLastActivity()->getTimestamp() > 3600 * 24) {
				throw new \Symfony\Component\Security\Core\Exception\UsernameNotFoundException('UserIdentity was too long unactive');
			}
			$userIdentity->setLastActivity(new DateTime());
		}
		
		$findParams = array(
			'id' => $userIdentity->getId(),
		);
		if ($userIdentity instanceof UniqueLoginInterface) {
			$findParams['loginToken'] = $userIdentity->getLoginToken();
		}
		$freshUserIdentity = $this->findOneBy($findParams);

		if ($freshUserIdentity === null) {
			throw new \Symfony\Component\Security\Core\Exception\UsernameNotFoundException('Unable to find an active admin');
		}
		
		return $freshUserIdentity;
	}

	/**
	 * @param string $class
	 * @return bool
	 */
	public function supportsClass($class) {
		return $this->getEntityName() === $class || is_subclass_of($class, $this->getEntityName());
	}

}
