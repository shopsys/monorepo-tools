<?php

namespace SS6\ShopBundle\Model\Product\Availability;

use Doctrine\ORM\Mapping as ORM;
use SS6\ShopBundle\Model\Product\Availability\AvailabilityData;

/**
 * @ORM\Table(name="availabilities")
 * @ORM\Entity
 */
class Availability {

	/**
	 * @var integer
	 *
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $id;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="string", length=100)
	 */
	private $name;

	/**
	 * @param \SS6\ShopBundle\Model\Product\Availability\AvailabilityData $availabilityData
	 */
	public function __construct(AvailabilityData $availabilityData) {
		$this->name = $availabilityData->getName();
	}

	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Availability\AvailabilityData $availabilityData
	 */
	public function edit(AvailabilityData $availabilityData) {
		$this->name = $availabilityData->getName();
	}
	
}
