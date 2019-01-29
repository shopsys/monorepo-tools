<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Model\Country;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(
 *     name="country_domains",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="country_domain", columns={"country_id", "domain_id"})
 *     }
 * )
 * @ORM\Entity
 */
class CountryDomain
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Country\Country
     *
     * @ORM\ManyToOne(targetEntity="Shopsys\FrameworkBundle\Model\Country\Country", inversedBy="domains")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $country;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    protected $domainId;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    protected $enabled = false;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    protected $priority;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Country\Country $country
     * @param int $domainId
     */
    public function __construct(Country $country, int $domainId)
    {
        $this->country = $country;
        $this->domainId = $domainId;
        $this->enabled = true;
        $this->priority = 0;
    }

    /**
     * @return int
     */
    public function getDomainId(): int
    {
        return $this->domainId;
    }

    /**
     * @param bool $enabled
     */
    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @return int
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * @param int $priority
     */
    public function setPriority(int $priority): void
    {
        $this->priority = $priority;
    }
}
