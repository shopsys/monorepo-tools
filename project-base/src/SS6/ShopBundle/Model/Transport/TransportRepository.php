<?php

namespace SS6\ShopBundle\Model\Transport;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Pricing\Vat\Vat;
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

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\Vat\Vat $vat
	 * @return \SS6\ShopBundle\Model\Transport\Transport[]
	 */
	public function getAllIncludingDeletedByVat(Vat $vat) {
		return $this->getTransportRepository()->findBy(array('vat' => $vat));
	}

	/**
	 * @param \SS6\ShopBundle\Model\Transport\Transport $transport
	 * @return \SS6\ShopBundle\Model\Transport\TransportDomain[]
	 */
	public function getTransportDomainByTransport(Transport $transport) {
		return $this->getTransportDomainRepository()->findBy(array('transport' => $transport));
	}
}