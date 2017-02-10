<?php

namespace Shopsys\ShopBundle\Model\Payment;

use Doctrine\ORM\EntityManager;
use Shopsys\ShopBundle\Model\Payment\Payment;
use Shopsys\ShopBundle\Model\Transport\Transport;

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
	 * @return \Doctrine\ORM\EntityRepository
	 */
	private function getPaymentDomainRepository() {
		return $this->em->getRepository(PaymentDomain::class);
	}

	/**
	 * @return \Doctrine\ORM\QueryBuilder
	 */
	public function getQueryBuilderForAll() {
		$qb = $this->getPaymentRepository()->createQueryBuilder('p')
			->where('p.deleted = :deleted')->setParameter('deleted', false)
			->orderBy('p.position')
			->addOrderBy('p.id');
		return $qb;
	}

	/**
	 * @return \Shopsys\ShopBundle\Model\Payment\Payment[]
	 */
	public function getAll() {
		return $this->getQueryBuilderForAll()->getQuery()->getResult();
	}

	/**
	 * @return \Shopsys\ShopBundle\Model\Payment\Payment[]
	 */
	public function getAllIncludingDeleted() {
		return $this->getPaymentRepository()->findAll();
	}

	/**
	 * @param int $id
	 * @return \Shopsys\ShopBundle\Model\Payment\Payment|null
	 */
	public function findById($id) {
		return $this->getPaymentRepository()->find($id);
	}

	/**
	 * @param int $id
	 * @return \Shopsys\ShopBundle\Model\Payment\Payment
	 */
	public function getById($id) {
		$payment = $this->findById($id);
		if ($payment === null) {
			throw new \Shopsys\ShopBundle\Model\Payment\Exception\PaymentNotFoundException('Payment with ID ' . $id . ' not found.');
		}
		return $payment;
	}

	/**
	 * @param int $id
	 * @return \Shopsys\ShopBundle\Model\Payment\Payment
	 */
	public function getByIdWithTransports($id) {
		try {
			return $this->em
				->createQuery('SELECT p, t FROM ' . Payment::class . ' p LEFT JOIN p.transports t WHERE p.id = :id')
				->setParameter('id', $id)
				->getSingleResult();
		} catch (\Doctrine\ORM\NoResultException $e) {
			throw new \Shopsys\ShopBundle\Model\Payment\Exception\PaymentNotFoundException('Payment with ID ' . $id . ' not found.', $e);
		}
	}

	/**
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getAllWithTransports() {
		return $this->getQueryBuilderForAll()
			->leftJoin(Transport::class, 't')
			->getQuery()
			->getResult();
	}

	/**
	 * @param \Shopsys\ShopBundle\Model\Transport\Transport $transport
	 * @return \Shopsys\ShopBundle\Model\Payment\Payment[]
	 */
	public function getAllByTransport(Transport $transport) {
		return $this->getQueryBuilderForAll()
			->join(Transport::class, 't')
			->andWhere('t.id = :transportId')
			->setParameter('transportId', $transport->getId())
			->getQuery()
			->getResult();
	}

	/**
	 * @param \Shopsys\ShopBundle\Model\Payment\Payment $payment
	 * @return \Shopsys\ShopBundle\Model\Payment\PaymentDomain[]
	 */
	public function getPaymentDomainsByPayment(Payment $payment) {
		return $this->getPaymentDomainRepository()->findBy(['payment' => $payment]);
	}

}
