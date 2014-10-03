<?php

namespace SS6\ShopBundle\Model\Transport;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Transport\Transport;

class TransportRepository {
	
	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;
	
	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 */
	public function __construct(EntityManager $em) {
		$this->em = $em;
	}
	
	/**
	 * @return \Doctrine\ORM\EntityRepository
	 */
	private function getTransportRepository() {
		return $this->em->getRepository(Transport::class);
	}
	
	/**
	 * @return array
	 */
	public function findAll() {
		return $this->findAllQueryBuilder()->getQuery()->getResult();
	}
	
	/**
	 * @param array $transportIds
	 * @return array
	 */
	public function findAllByIds(array $transportIds) {
		$dql = sprintf('SELECT t FROM %s t WHERE t.deleted = :deleted AND t.id IN (:trasportIds)', Transport::class);
		return $this->em->createQuery($dql)
			->setParameter('deleted', false)
			->setParameter('trasportIds', $transportIds)
			->getResult();
	}
	
	/**
	 * @return \Doctrine\ORM\QueryBuilder
	 */
	private function findAllQueryBuilder() {
		$qb = $this->getTransportRepository()->createQueryBuilder('t')
			->where('t.deleted = :deleted')->setParameter('deleted', false);
		return $qb;
	}
	
	/**
	 * @return array
	 */
	public function findAllIncludingDeleted() {
		return $this->getTransportRepository()->findAll();
	}

	/**
	 * @param int $id
	 * @return \SS6\ShopBundle\Model\Transport\Transport|null
	 */
	public function findById($id) {
		return $this->getTransportRepository()->find($id);
	}
	
	/**
	 * @param int $id
	 * @return \SS6\ShopBundle\Model\Transport\Transport
	 * @throws TransportNotFoundException
	 */
	public function getById($id) {
		$transport = $this->findById($id);
		if ($transport === null) {
			throw new \SS6\ShopBundle\Model\Transport\Exception\TransportNotFoundException(array('id' => $id));
		}
		return $transport;
	}
}