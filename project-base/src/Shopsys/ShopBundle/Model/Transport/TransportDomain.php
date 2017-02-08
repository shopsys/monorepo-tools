<?php

namespace SS6\ShopBundle\Model\Transport;

use Doctrine\ORM\Mapping as ORM;
use SS6\ShopBundle\Model\Transport\Transport;

/**
 * @ORM\Table(name="transport_domains")
 * @ORM\Entity
 */
class TransportDomain {

	/**
	 * @var \SS6\ShopBundle\Model\Transport\Transport
	 *
	 * @ORM\Id
	 * @ORM\ManyToOne(targetEntity="SS6\ShopBundle\Model\Transport\Transport")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $transport;

	/**
	 * @var int
	 *
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 */
	private $domainId;

	/**
	 * @param \SS6\ShopBundle\Model\Transport\Transport $transport
	 * @param int $domainId
	 */
	public function __construct(Transport $transport, $domainId) {
		$this->transport = $transport;
		$this->domainId = $domainId;
	}

	/**
	 * @return int
	 */
	public function getDomainId() {
		return $this->domainId;
	}

}
