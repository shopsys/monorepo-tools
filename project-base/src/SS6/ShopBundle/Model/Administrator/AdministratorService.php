<?php

namespace SS6\ShopBundle\Model\Administrator;

use Symfony\Component\Security\Core\Encoder\EncoderFactory;

class AdministratorService {

	/**
	 * @var Symfony\Component\Security\Core\Encoder\EncoderFactory
	 */
	private $encoderFactory;

	/**
	 * @param \Symfony\Component\Security\Core\Encoder\EncoderFactory $encoderFactory
	 */
	public function __construct(EncoderFactory $encoderFactory) {
		$this->encoderFactory = $encoderFactory;
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
}
