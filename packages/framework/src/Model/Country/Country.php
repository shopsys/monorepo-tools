<?php

namespace Shopsys\FrameworkBundle\Model\Country;

use Doctrine\ORM\Mapping as ORM;

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
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    protected $name;

    /**
     * Country code in ISO 3166-1 alpha-2
     * @var string|null
     *
     * @ORM\Column(type="string", length=2, nullable=true)
     */
    protected $code;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    protected $domainId;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Country\CountryData $countryData
     * @param int $domainId
     */
    public function __construct(CountryData $countryData, $domainId)
    {
        $this->name = $countryData->name;
        $this->domainId = $domainId;
        $this->code = $countryData->code;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Country\CountryData $countryData
     */
    public function edit(CountryData $countryData)
    {
        $this->name = $countryData->name;
        $this->code = $countryData->code;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getDomainId()
    {
        return $this->domainId;
    }

    /**
     * @return null|string
     */
    public function getCode()
    {
        return $this->code;
    }
}
