<?php

namespace SS6\ShopBundle\Model\Order;

use Doctrine\ORM\EntityManager;

class OrderRepository {
	
	/** 
	 * @var \Doctrine\ORM\EntityRepository
	 */
	private $entityRepository;

	/**
	 * @param \Doctrine\ORM\EntityManager $entityManager
	 */
	public function __construct(EntityManager $entityManager) {
		$this->entityRepository = $entityManager->getRepository(Order::class);
	}
	
	/**
	 * @param int $userId
	 * @return \SS6\ShopBundle\Model\Order\Order[]
	 */
	public function findByUserId($userId) {
		return $this->entityRepository->findBy(array(
			'customer' => $userId,
		));
	}
	
}
