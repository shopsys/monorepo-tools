<?php

namespace SS6\CoreBundle\Model\Transport\Facade;

use Doctrine\ORM\EntityManager;
use SS6\CoreBundle\Model\Transport\Entity\Transport;
use SS6\CoreBundle\Model\Transport\Repository\TransportRepository;

class TransportEditFacade {
	
	/**
	 * @var EntityManager
	 */
	private $em;
	
	/**
	 * @var TransportRepository
	 */
	private $transportRepository;

	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 */
	public function __construct(EntityManager $em, TransportRepository $transportRepository) {
		$this->em = $em;
		$this->transportRepository = $transportRepository;
	}
	
	/**
	 * @param \SS6\CoreBundle\Model\Transport\Entity\Transport $transport
	 */
	public function create(Transport $transport) {
		$this->em->persist($transport);
		$this->em->flush();
	}
	
	/**
	 * @param \SS6\CoreBundle\Model\Transport\Entity\Transport $transport
	 */
	public function edit(Transport $transport) {
		$this->em->persist($transport);
		$this->em->flush();
	}
	
	/**
	 * @param int $id
	 * @return \SS6\CoreBundle\Model\Transport\Entity\Transport
	 */
	public function getById($id) {
		return $this->transportRepository->getById($id);
	}
}
