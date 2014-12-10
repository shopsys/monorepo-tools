<?php

namespace SS6\ShopBundle\Model\Administrator;

use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\SecurityContext;

class AdministratorService {

	/**
	 * @var Symfony\Component\Security\Core\Encoder\EncoderFactory
	 */
	private $encoderFactory;

	/**
	 * @var Symfony\Component\Security\Core\SecurityContext
	 */
	private $securityContext;

	/**
	 * @param \Symfony\Component\Security\Core\Encoder\EncoderFactory $encoderFactory
	 */
	public function __construct(
		EncoderFactory $encoderFactory,
		SecurityContext $securityContext
	) {
		$this->encoderFactory = $encoderFactory;
		$this->securityContext = $securityContext;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Administrator\Administrator $administrator
	 * @param string $password
	 * @return string
	 */
	public function getPasswordHash(Administrator $administrator, $password) {
		$encoder = $this->encoderFactory->getEncoder($administrator);
		$passwordHash = $encoder->encodePassword($password, $administrator->getSalt());

		return $passwordHash;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Administrator\Administrator $administrator
	 * @param int $adminCount
	 */
	public function delete(Administrator $administrator, $adminCount) {
		if ($adminCount === 1) {
			throw new \SS6\ShopBundle\Model\Administrator\Exception\DeletingLastAdministratorException();
		}
		if ($this->securityContext->getToken()->getUser() === $administrator) {
			throw new \SS6\ShopBundle\Model\Administrator\Exception\DeletingSelfException();
		}
	}

	/**
	 * @param \SS6\ShopBundle\Model\Administrator\AdministratorData $administratorData
	 * @param \SS6\ShopBundle\Model\Administrator\Administrator $administrator
	 * @param \SS6\ShopBundle\Model\Administrator\Administrator|null $administratorByUserName
	 * @return \SS6\ShopBundle\Model\Administrator\Administrator
	 */
	public function edit(
		AdministratorData $administratorData,
		Administrator $administrator,
		Administrator $administratorByUserName = null
	) {
		if ($administratorByUserName !== null
			&& $administratorByUserName !== $administrator
			&& $administratorByUserName->getUsername() === $administratorData->getUsername()
		) {
			throw new \SS6\ShopBundle\Model\Administrator\Exception\DuplicateUserNameException($administrator->getUsername());
		}
		$administrator->edit($administratorData);
		if ($administratorData->getPassword() !== null) {
			$administrator->setPassword($this->getPasswordHash($administrator, $administratorData->getPassword()));
		}

		return $administrator;
	}

}
