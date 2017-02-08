<?php

namespace SS6\ShopBundle\Model\Order;

use SS6\ShopBundle\Component\String\HashGenerator;
use SS6\ShopBundle\Model\Order\OrderRepository;

class OrderHashGeneratorRepository {

	const HASH_LENGTH = 50;
	const MAX_GENERATE_TRIES = 100;

	/**
	 * @var \SS6\ShopBundle\Model\Order\OrderRepository
	 */
	private $orderRepository;

	/**
	 * @var \SS6\ShopBundle\Component\String\HashGenerator
	 */
	private $hashGenerator;

	public function __construct(
		OrderRepository $orderRepository,
		HashGenerator $hashGenerator
	) {
		$this->orderRepository = $orderRepository;
		$this->hashGenerator = $hashGenerator;
	}

	/**
	 * @return string
	 */
	public function getUniqueHash() {
		$triesCount = 0;
		do {
			$hash = $this->hashGenerator->generateHash(self::HASH_LENGTH);
			$order = $this->orderRepository->findByUrlHashIncludingDeletedOrders($hash);
			$triesCount++;
			if ($triesCount > self::MAX_GENERATE_TRIES) {
				throw new \SS6\ShopBundle\Model\Order\Exception\OrderHashGenerateException('Trying generate hash reached the limit.');
			}
		} while ($order !== null);
		return $hash;
	}

}
