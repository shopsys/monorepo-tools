<?php

namespace SS6\ShopBundle\Model\Payment\Repository;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Payment\Entity\Payment;
use SS6\ShopBundle\Model\Transport\Entity\Transport;
use SS6\ShopBundle\Model\Payment\Exception\PaymentNotFoundException;

class PaymentRepository {
	
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
	private function getPaymentRepository() {
		return $this->em->getRepository(Payment::class);
	}
	
	/**
	 * @return array
	 */
	public function getAll() {
		return $this->getPaymentRepository()->findBy(array('deleted' => false), array('name' => 'ASC'));
	}
	
	/**
	 * @return array
	 */
	public function getAllIncludingDeleted() {
		return $this->getPaymentRepository()->findAll();
	}
	
	/**
	 * @param int $id
	 * @return \SS6\ShopBundle\Model\Payment\Entity\Transport
	 * @throws PaymentNotFoundException
	 */
	public function getById($id) {
		$criteria = array('id' => $id);
		$payment = $this->getPaymentRepository()->findOneBy($criteria);
		if ($payment === null) {
			throw new PaymentNotFoundException($criteria);
		}
		return $payment;
	}
	
	/**
	 * @param int $id
	 * @return \SS6\ShopBundle\Model\Payment\Entity\Transport
	 * @throws PaymentNotFoundException
	 */
	public function getByIdWithTransports($id) {
		$criteria = array('id' => $id);
		try {
			$dql = sprintf('SELECT p, t FROM %s p LEFT JOIN p.transports t WHERE p.id = :id', Payment::class);
			return $this->em->createQuery($dql)->setParameters($criteria)->getSingleResult();
		} catch (\Doctrine\ORM\NoResultException $e) {
			throw new PaymentNotFoundException($criteria, $e);
		}
	}
	
	/**
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getAllWithTransports() {
		$dql = sprintf('SELECT p, t FROM %s p LEFT JOIN p.transports t WHERE p.deleted = :deleted', Payment::class);
		return $this->em->createQuery($dql)->setParameter('deleted', false)->getResult();
	}
	
	/**
	 * @param \SS6\ShopBundle\Model\Transport\Entity\Transport $transport
	 * @return array
	 */
	public function getAllByTransport(Transport $transport) {
		$dql = sprintf('SELECT p, t FROM %s p JOIN p.transports t WHERE t.id = :transportId', Payment::class);
		return $this->em->createQuery($dql)->setParameter('transportId', $transport->getId())->getResult();
	}
}