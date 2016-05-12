<?php

namespace SS6\ShopBundle\Model\Country;

use Doctrine\ORM\Mapping as ORM;
use SS6\ShopBundle\Model\Country\CountryData;

/**
 * @ORM\Table(name="countries")
 * @ORM\Entity
 */
class Country {

	/**
	 * @var int
	 *
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $id;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="string", length=255)
	 */
	private $name;

	/**
	 * @var bool
	 *
	 * @ORM\Column(type="boolean")
	 */
	private $visible;

	/**
	 * @var int
	 *
	 * @ORM\Column(type="integer")
	 */
	private $domainId;

	/**
	 * @param \SS6\ShopBundle\Model\Country\CountryData $countryData
	 * @param int $domainId
	 */
	public function __construct(CountryData $countryData, $domainId) {
		$this->name = $countryData->name;
		$this->visible = $countryData->visible;
		$this->domainId = $domainId;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Country\CountryData $countryData
	 */
	public function edit(CountryData $countryData) {
		$this->name = $countryData->name;
		$this->visible = $countryData->visible;
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
	 * @return bool
	 */
	public function isVisible() {
		return $this->visible;
	}

	/**
	 * @return int
	 */
	public function getDomainId() {
		return $this->domainId;
	}

}
