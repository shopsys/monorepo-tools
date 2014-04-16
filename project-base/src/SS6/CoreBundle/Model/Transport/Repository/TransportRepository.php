<?php

namespace SS6\CoreBundle\Model\Transport\Repository;

use Doctrine\ORM\EntityManager;
use SS6\CoreBundle\Model\Transport\Entity\Transport;
use SS6\CoreBundle\Model\Transport\Exception\TransportNotFoundException;

class TransportRepository {
	
	/**
	 * @var EntityManager
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
	public function getAll() {
		return $this->getAllQueryBuilder()->getQuery()->getResult();
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
	private function getAllQueryBuilder() {
		$qb = $this->getTransportRepository()->createQueryBuilder('t')
			->where('t.deleted = :deleted')->setParameter('deleted', false);
		return $qb;
	}
	
	/**
	 * @return array
	 */
	public function getAllIncludingDeleted() {
		return $this->getTransportRepository()->findAll();
	}
	
	/**
	 * @param int $id
	 * @return \SS6\CoreBundle\Model\Transport\Entity\Transport
	 * @throws TransportNotFoundException
	 */
	public function getById($id) {
		$criteria = array('id' => $id);
		$transport = $this->getTransportRepository()->findOneBy($criteria);
		if ($transport === null) {
			throw new TransportNotFoundException($criteria);
		}
		return $transport;
	}
	
	/**
	 * @param array $allPayments
	 * @return array
	 */
	public function getAllDataWithVisibility(array $allPayments) {
		$transports = $this->getAllQueryBuilder()->getQuery()->getResult();
		$transportsData = [];
		
		foreach ($transports as $transport) {
			/* @var $transport \SS6\CoreBundle\Model\Transport\Entity\Transport */
			$visible = false;
			if (!$transport->isHidden()) {
				$visible = $this->existVisiblePaymentWithTransport($allPayments, $transport);
			}
			$transportsData[] = array(
				'entity' => $transport,
				'visible' => $visible,
			);
		}
		
		return $transportsData;
	}
	
	/**
	 * @param array $allPayments
	 * @param \SS6\CoreBundle\Model\Transport\Entity\Transport $transport
	 * @return boolean
	 */
	private function existVisiblePaymentWithTransport(array $allPayments, Transport $transport) {
		foreach ($allPayments as $payment) {
			/* @var $payment \SS6\CoreBundle\Model\Payment\Entity\Payment */
			if ($payment->isVisible() && $payment->getTransports()->contains($transport)) {
				return true;
			}
		}
		return false;
	}
}