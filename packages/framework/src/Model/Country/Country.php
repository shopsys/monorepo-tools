<?php

namespace Shopsys\FrameworkBundle\Model\Country;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use Shopsys\FrameworkBundle\Model\Country\Exception\CountryDomainNotFoundException;
use Shopsys\FrameworkBundle\Model\Localization\AbstractTranslatableEntity;

/**
 * @ORM\Table(name="countries")
 * @ORM\Entity
 *
 * @method CountryTranslation translation(?string $locale = null)
 */
class Country extends AbstractTranslatableEntity
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
     * Country code in ISO 3166-1 alpha-2
     * @var string|null
     *
     * @ORM\Column(type="string", length=2)
     */
    protected $code;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Country\CountryTranslation[]|\Doctrine\Common\Collections\Collection
     *
     * @Prezent\Translations(targetEntity="Shopsys\FrameworkBundle\Model\Country\CountryTranslation")
     */
    protected $translations;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Country\CountryDomain[]|\Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Shopsys\FrameworkBundle\Model\Country\CountryDomain", mappedBy="country", cascade={"persist"}, fetch="EXTRA_LAZY")
     */
    protected $domains;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Country\CountryData $countryData
     */
    public function __construct(CountryData $countryData)
    {
        $this->translations = new ArrayCollection();
        $this->domains = new ArrayCollection();
        $this->setTranslations($countryData);
        $this->createDomains($countryData);
        $this->code = $countryData->code;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Country\CountryData $countryData
     */
    protected function setTranslations(CountryData $countryData): void
    {
        foreach ($countryData->names as $locale => $name) {
            $this->translation($locale)->setName($name);
        }
    }

    /**
     * @param string|null $locale
     * @return string
     */
    public function getName($locale = null): string
    {
        return $this->translation($locale)->getName();
    }

    /**
     * @param int $domainId
     * @return bool
     */
    public function isEnabled(int $domainId): bool
    {
        return $this->getCountryDomain($domainId)->isEnabled();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Country\CountryData $countryData
     */
    public function edit(CountryData $countryData): void
    {
        $this->code = $countryData->code;
        $this->setTranslations($countryData);
        $this->setDomains($countryData);
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return null|string
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * @param int $domainId
     * @return int
     */
    public function getPriority(int $domainId): int
    {
        return $this->getCountryDomain($domainId)->getPriority();
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Country\CountryTranslation
     */
    protected function createTranslation(): CountryTranslation
    {
        return new CountryTranslation();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Country\CountryData $countryData
     */
    protected function setDomains(CountryData $countryData): void
    {
        foreach ($this->domains as $countryDomain) {
            $domainId = $countryDomain->getDomainId();
            $countryDomain->setEnabled($countryData->enabled[$domainId]);
            $countryDomain->setPriority($countryData->priority[$domainId] ?? 0);
        }
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Country\CountryData $countryData
     */
    protected function createDomains(CountryData $countryData): void
    {
        $domainIds = array_keys($countryData->enabled);

        foreach ($domainIds as $domainId) {
            $countryDomain = new CountryDomain($this, $domainId);
            $this->domains->add($countryDomain);
        }

        $this->setDomains($countryData);
    }

    /**
     * @param int $domainId
     * @return \Shopsys\FrameworkBundle\Model\Country\CountryDomain
     */
    protected function getCountryDomain(int $domainId): CountryDomain
    {
        foreach ($this->domains as $countryDomain) {
            if ($countryDomain->getDomainId() === $domainId) {
                return $countryDomain;
            }
        }

        throw new CountryDomainNotFoundException($domainId, $this->id);
    }
}
