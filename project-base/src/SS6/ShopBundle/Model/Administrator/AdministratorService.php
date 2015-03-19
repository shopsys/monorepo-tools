<?php

namespace SS6\ShopBundle\Model\Administrator;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;

class AdministratorService {

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
		if ($this->tokenStorage->getToken()->getUser() === $administrator) {
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
			&& $administratorByUserName->getUsername() === $administratorData->username
		) {
			throw new \SS6\ShopBundle\Model\Administrator\Exception\DuplicateUserNameException($administrator->getUsername());
		}
		$administrator->edit($administratorData);
		if ($administratorData->password !== null) {
			$administrator->setPassword($this->getPasswordHash($administrator, $administratorData->password));
		}

		return $administrator;
	}

}
