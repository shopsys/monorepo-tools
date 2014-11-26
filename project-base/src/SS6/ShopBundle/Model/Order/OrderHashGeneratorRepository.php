<?php

namespace SS6\ShopBundle\Model\Order;

use SS6\ShopBundle\Model\Order\OrderRepository;

class OrderHashGeneratorRepository {

	const HASH_LENGTH = 50;
	const MAX_GENERATE_TRIES = 100;

	/**
	 * @var \SS6\ShopBundle\Model\Order\OrderRepository
	 */
	private $orderRepository;

	public function __construct(OrderRepository $orderRepository) {
		$this->orderRepository = $orderRepository;
	}

	/**
	 * @return string
	 */
	public function getUniqueHash() {
		$triesCount = 0;
		do {
			$hash = $this->generateHash();
			$order = $this->orderRepository->findByUrlHash($hash);
			$triesCount++;
			if ($triesCount > self::MAX_GENERATE_TRIES) {
				throw new \SS6\ShopBundle\Model\Order\Exception\OrderHashGenerateException('Trying generate hash reached the limit.');
			}
		} while ($order !== null);
		return $hash;
	}

	private function generateHash() {
		$hashBytesSize = (int)floor(self::HASH_LENGTH/2);
		$hashBytes = mcrypt_create_iv($hashBytesSize, MCRYPT_DEV_URANDOM);
		return bin2hex($hashBytes);
	}
}
