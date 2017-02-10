<?php

namespace Shopsys\ShopBundle\Model\Country;

use Doctrine\ORM\Mapping as ORM;
use Shopsys\ShopBundle\Model\Country\CountryData;

/**
 * @ORM\Table(name="countries")
 * @ORM\Entity
 */
class Country
{

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
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $domainId;

    /**
     * @param \Shopsys\ShopBundle\Model\Country\CountryData $countryData
     * @param int $domainId
     */
    public function __construct(CountryData $countryData, $domainId) {
        $this->name = $countryData->name;
        $this->domainId = $domainId;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Country\CountryData $countryData
     */
    public function edit(CountryData $countryData) {
        $this->name = $countryData->name;
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
     * @return int
     */
    public function getDomainId() {
        return $this->domainId;
    }
}
