<?php

namespace Shopsys\ShopBundle\Model\Transport;

use Doctrine\ORM\EntityManager;
use Shopsys\ShopBundle\Model\Transport\TransportPrice;

class TransportPriceRepository {

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
	private function getTransportPriceRepository() {
		return $this->em->getRepository(TransportPrice::class);
	}

	/**
	 * @param \Shopsys\ShopBundle\Model\Transport\Transport $transport
	 * @return \Shopsys\ShopBundle\Model\Transport\TransportPrice[]
	 */
	public function getAllByTransport(Transport $transport) {
		return $this->getTransportPriceRepository()->findBy(['transport' => $transport]);
	}

}
