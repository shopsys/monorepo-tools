<?php

namespace SS6\CoreBundle\Model\Transport\Facade;

use Doctrine\ORM\EntityManager;
use SS6\CoreBundle\Model\Transport\Entity\Transport;

class TransportEditFacade {
	
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
	 * @param \SS6\CoreBundle\Model\Transport\Entity\Transport $transport
	 */
	public function create(Transport $transport) {
		$this->em->persist($transport);
		$this->em->flush();
	}
}
