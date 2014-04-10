<?php

namespace SS6\CoreBundle\Model\Transport\Repository;

use Doctrine\ORM\EntityManager;
use SS6\CoreBundle\Model\Transport\Exception\TransportNotFoundException;

class TransportRepository {
	
	/**
	 * @var EntityRepository
	 */
	private $repository;
	
	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 */
	public function __construct(EntityManager $em) {
		$this->repository = $em->getRepository('SS6CoreBundle:Transport\Entity\Transport');
	}
	
	/**
	 * @return array
	 */
	public function getAllUndeleted() {
		return $this->getAllUndeletedQueryBuilder()->getQuery()->getResult();
	}
	
	/**
	 * @return \Doctrine\ORM\QueryBuilder
	 */
	public function getAllUndeletedQueryBuilder() {
		$qb = $this->repository->createQueryBuilder('t')
			->where('t.deleted = :deleted')->setParameter('deleted', false);
		return $qb;
	}
	
	/**
	 * @param int $id
	 * @return \SS6\CoreBundle\Model\Transport\Entity\Transport
	 * @throws TransportNotFoundException
	 */
	public function getById($id) {
		$criteria = array('id' => $id);
		$transport = $this->repository->findOneBy($criteria);
		if ($transport === null) {
			throw new TransportNotFoundException($criteria);
		}
		return $transport;
	}
}