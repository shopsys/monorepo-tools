<?php

namespace SS6\ShopBundle\Model\Payment;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Payment\Payment;
use SS6\ShopBundle\Model\Pricing\Vat\Vat;
use SS6\ShopBundle\Model\Transport\Transport;

class PaymentRepository {
	
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
	private function getPaymentRepository() {
		return $this->em->getRepository(Payment::class);
	}
	
	/**
	 * @return array
	 */
	public function findAll() {
		return $this->getPaymentRepository()->findBy(array('deleted' => false), array('name' => 'ASC'));
	}
	
	/**
	 * @return array
	 */
	public function findAllIncludingDeleted() {
		return $this->getPaymentRepository()->findAll();
	}

	/**
	 * @param int $id
	 * @return \SS6\ShopBundle\Model\Payment\Payment|null
	 */
	public function findById($id) {
		return $this->getPaymentRepository()->find($id);
	}
	
	/**
	 * @param int $id
	 * @return \SS6\ShopBundle\Model\Payment\Payment
	 * @throws PaymentNotFoundException
	 */
	public function getById($id) {
		$payment = $this->findById($id);
		if ($payment === null) {
			throw new Exception\PaymentNotFoundException(array('id' => $id));
		}
		return $payment;
	}
	
	/**
	 * @param int $id
	 * @return \SS6\ShopBundle\Model\Payment\Payment
	 * @throws PaymentNotFoundException
	 */
	public function getByIdWithTransports($id) {
		$criteria = array('id' => $id);
		try {
			$dql = sprintf('SELECT p, t FROM %s p LEFT JOIN p.transports t WHERE p.id = :id', Payment::class);
			return $this->em->createQuery($dql)->setParameters($criteria)->getSingleResult();
		} catch (\Doctrine\ORM\NoResultException $e) {
			throw new Exception\PaymentNotFoundException($criteria, $e);
		}
	}
	
	/**
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function findAllWithTransports() {
		$dql = sprintf('SELECT p, t FROM %s p LEFT JOIN p.transports t WHERE p.deleted = :deleted', Payment::class);
		return $this->em->createQuery($dql)->setParameter('deleted', false)->getResult();
	}
	
	/**
	 * @param \SS6\ShopBundle\Model\Transport\Transport $transport
	 * @return array
	 */
	public function findAllByTransport(Transport $transport) {
		$dql = sprintf('SELECT p, t FROM %s p JOIN p.transports t WHERE t.id = :transportId', Payment::class);
		return $this->em->createQuery($dql)->setParameter('transportId', $transport->getId())->getResult();
	}

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\Vat\Vat $vat
	 * @return \SS6\ShopBundle\Model\Payment\Payment[]
	 */
	public function getAllIncludingDeletedByVat(Vat $vat) {
		return $this->getPaymentRepository()->findBy(array('vat' => $vat));
	}
}
