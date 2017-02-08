<?php

namespace SS6\ShopBundle\Model\Transport;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Join;
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
	 * @return \Doctrine\ORM\EntityRepository
	 */
	private function getTransportDomainRepository() {
		return $this->em->getRepository(TransportDomain::class);
	}

	/**
	 * @return \SS6\ShopBundle\Model\Transport\Transport[]
	 */
	public function getAll() {
		return $this->getQueryBuilderForAll()->getQuery()->getResult();
	}

	/**
	 * @param array $transportIds
	 * @return \SS6\ShopBundle\Model\Transport\Transport[]
	 */
	public function getAllByIds(array $transportIds) {
		$dql = sprintf('SELECT t FROM %s t WHERE t.deleted = :deleted AND t.id IN (:trasportIds)', Transport::class);
		return $this->em->createQuery($dql)
			->setParameter('deleted', false)
			->setParameter('trasportIds', $transportIds)
			->getResult();
	}

	/**
	 * @param int $domainId
	 * @return \SS6\ShopBundle\Model\Transport\Transport[]
	 */
	public function getAllByDomainId($domainId) {
		$qb = $this->getQueryBuilderForAll()
			->join(TransportDomain::class, 'td', Join::WITH, 't.id = td.transport AND td.domainId = :domainId')
			->setParameter('domainId', $domainId);

		return $qb->getQuery()->getResult();
	}

	/**
	 * @return \Doctrine\ORM\QueryBuilder
	 */
	public function getQueryBuilderForAll() {
		$qb = $this->getTransportRepository()->createQueryBuilder('t')
			->where('t.deleted = :deleted')->setParameter('deleted', false)
			->orderBy('t.position')
			->addOrderBy('t.id');
		return $qb;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Transport\Transport[]
	 */
	public function getAllIncludingDeleted() {
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
	 */
	public function getById($id) {
		$transport = $this->findById($id);
		if ($transport === null) {
			throw new \SS6\ShopBundle\Model\Transport\Exception\TransportNotFoundException('Transport with ID ' . $id . ' not found.');
		}
		return $transport;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Transport\Transport $transport
	 * @return \SS6\ShopBundle\Model\Transport\TransportDomain[]
	 */
	public function getTransportDomainsByTransport(Transport $transport) {
		return $this->getTransportDomainRepository()->findBy(['transport' => $transport]);
	}
}
