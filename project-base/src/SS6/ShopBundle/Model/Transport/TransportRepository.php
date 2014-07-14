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
	 * @return \SS6\ShopBundle\Model\Transport\Transport
	 * @throws TransportNotFoundException
	 */
	public function getById($id) {
		$criteria = array('id' => $id);
		$transport = $this->getTransportRepository()->findOneBy($criteria);
		if ($transport === null) {
			throw new \SS6\ShopBundle\Model\Transport\Exception\TransportNotFoundException($criteria);
		}
		return $transport;
	}
	
	/**
	 * @param array $allPayments
	 * @return array
	 */
	public function findAllDataWithVisibility(array $allPayments) {
		$transports = $this->findAllQueryBuilder()->addOrderBy('t.name')->getQuery()->getResult();
		$transportsData = [];
		
		foreach ($transports as $transport) {
			/* @var $transport \SS6\ShopBundle\Model\Transport\Transport */
			$visible = false;
			if (!$transport->isHidden()) {
				$visible = $this->existsVisiblePaymentWithTransport($allPayments, $transport);
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
	 * @param \SS6\ShopBundle\Model\Transport\Transport $transport
	 * @return boolean
	 */
	private function existsVisiblePaymentWithTransport(array $allPayments, Transport $transport) {
		foreach ($allPayments as $payment) {
			/* @var $payment \SS6\ShopBundle\Model\Payment\Payment */
			if ($payment->isVisible() && $payment->getTransports()->contains($transport)) {
				return true;
			}
		}
		return false;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Payment\Payment[] $visiblePayments
	 * @return \SS6\ShopBundle\Model\Transport\Transport[]
	 */
	public function getVisible(array $visiblePayments) {
		$transportsWithVisibility = $this->findAllDataWithVisibility($visiblePayments);

		$visibleTransports = array();
		foreach ($transportsWithVisibility as $transportWithVisibility) {
			if ($transportWithVisibility['visible']) {
				$visibleTransports[] = $transportWithVisibility['entity'];
			}
		}

		return $visibleTransports;
	}
}
