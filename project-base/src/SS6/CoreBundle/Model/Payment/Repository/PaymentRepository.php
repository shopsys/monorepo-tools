<?php

namespace SS6\CoreBundle\Model\Payment\Repository;

use Doctrine\ORM\EntityManager;
use SS6\CoreBundle\Model\Payment\Exception\PaymentNotFoundException;

class PaymentRepository {
	
	/**
	 * @var EntityRepository
	 */
	private $repository;
	
	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 */
	public function __construct(EntityManager $em) {
		$this->repository = $em->getRepository('SS6CoreBundle:Payment\Entity\Payment');
	}
	
	/**
	 * @return array
	 */
	public function getAllUndeleted() {
		return $this->repository->findBy(array('deleted' => false), array('name' => 'ASC'));
	}
	
	/**
	 * @param int $id
	 * @return \SS6\CoreBundle\Model\Payment\Entity\Transport
	 * @throws TransportNotFoundException
	 */
	public function getById($id) {
		$criteria = array('id' => $id);
		$transport = $this->repository->findOneBy($criteria);
		if ($transport === null) {
			throw new PaymentNotFoundException($criteria);
		}
		return $transport;
	}
}