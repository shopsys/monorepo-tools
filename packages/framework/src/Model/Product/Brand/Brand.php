<?php

namespace Shopsys\FrameworkBundle\Model\Product\Brand;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use Shopsys\FrameworkBundle\Model\Localization\AbstractTranslatableEntity;
use Shopsys\FrameworkBundle\Model\Product\Brand\Exception\BrandDomainNotFoundException;

/**
 * @ORM\Table(name="brands")
 * @ORM\Entity
 *
 * @method BrandTranslation translation(?string $locale = null)
 */
class Brand extends AbstractTranslatableEntity
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
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    protected $name;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Brand\BrandTranslation[]|\Doctrine\Common\Collections\Collection
     *
     * @Prezent\Translations(targetEntity="Shopsys\FrameworkBundle\Model\Product\Brand\BrandTranslation")
     */
    protected $translations;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Brand\BrandDomain[]|\Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Shopsys\FrameworkBundle\Model\Product\Brand\BrandDomain", mappedBy="brand", cascade={"persist"}, fetch="EXTRA_LAZY")
     */
    protected $domains;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Brand\BrandData $brandData
     */
    public function __construct(BrandData $brandData)
    {
        $this->name = $brandData->name;
        $this->translations = new ArrayCollection();
        $this->domains = new ArrayCollection();

        $this->setTranslations($brandData);
        $this->createDomains($brandData);
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
     * @param \Shopsys\FrameworkBundle\Model\Product\Brand\BrandData $brandData
     */
    public function edit(BrandData $brandData)
    {
        $this->name = $brandData->name;
        $this->setTranslations($brandData);
        $this->setDomains($brandData);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Brand\BrandData $brandData
     */
    protected function setTranslations(BrandData $brandData)
    {
        foreach ($brandData->descriptions as $locale => $description) {
            $this->translation($locale)->setDescription($description);
        }
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Product\Brand\BrandTranslation
     */
    protected function createTranslation()
    {
        return new BrandTranslation();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Brand\BrandData $brandData
     */
    protected function setDomains(BrandData $brandData)
    {
        foreach ($this->domains as $brandDomain) {
            $domainId = $brandDomain->getDomainId();
            $brandDomain->setSeoTitle($brandData->seoTitles[$domainId]);
            $brandDomain->setSeoH1($brandData->seoH1s[$domainId]);
            $brandDomain->setSeoMetaDescription($brandData->seoMetaDescriptions[$domainId]);
        }
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Brand\BrandData $brandData
     */
    protected function createDomains(BrandData $brandData)
    {
        $domainIds = array_keys($brandData->seoTitles);

        foreach ($domainIds as $domainId) {
            $brandDomain = new BrandDomain($this, $domainId);
            $this->domains->add($brandDomain);
        }

        $this->setDomains($brandData);
    }

    /**
     * @param int $domainId
     * @return \Shopsys\FrameworkBundle\Model\Product\Brand\BrandDomain
     */
    protected function getBrandDomain(int $domainId)
    {
        foreach ($this->domains as $domain) {
            if ($domain->getDomainId() === $domainId) {
                return $domain;
            }
        }

        throw new BrandDomainNotFoundException($this->id, $domainId);
    }

    /**
     * @param int $domainId
     * @return string|null
     */
    public function getSeoTitle(int $domainId)
    {
        return $this->getBrandDomain($domainId)->getSeoTitle();
    }

    /**
     * @param int $domainId
     * @return string|null
     */
    public function getSeoMetaDescription(int $domainId)
    {
        return $this->getBrandDomain($domainId)->getSeoMetaDescription();
    }

    /**
     * @param int $domainId
     * @return string|null
     */
    public function getSeoH1(int $domainId)
    {
        return $this->getBrandDomain($domainId)->getSeoH1();
    }

    /**
     * @param string $locale
     * @return string
     */
    public function getDescription($locale = null)
    {
        return $this->translation($locale)->getDescription();
    }
}
