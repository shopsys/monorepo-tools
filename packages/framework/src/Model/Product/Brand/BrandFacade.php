<?php

namespace Shopsys\FrameworkBundle\Model\Product\Brand;

use Doctrine\ORM\EntityManager;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\Image\ImageFacade;
use Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrlFacade;

class BrandFacade
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Brand\BrandRepository
     */
    private $brandRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Image\ImageFacade
     */
    private $imageFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrlFacade
     */
    private $friendlyUrlFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    private $domain;

    public function __construct(
        EntityManager $em,
        BrandRepository $brandRepository,
        ImageFacade $imageFacade,
        FriendlyUrlFacade $friendlyUrlFacade,
        Domain $domain
    ) {
        $this->em = $em;
        $this->brandRepository = $brandRepository;
        $this->imageFacade = $imageFacade;
        $this->friendlyUrlFacade = $friendlyUrlFacade;
        $this->domain = $domain;
    }

    /**
     * @param int $brandId
     * @return \Shopsys\FrameworkBundle\Model\Product\Brand\Brand
     */
    public function getById($brandId)
    {
        return $this->brandRepository->getById($brandId);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Brand\BrandEditData $brandEditData
     * @return \Shopsys\FrameworkBundle\Model\Product\Brand\Brand
     */
    public function create(BrandEditData $brandEditData)
    {
        $domains = $this->domain->getAll();
        $brandData = $brandEditData->getBrandData();
        $brand = new Brand($brandData);
        $this->em->persist($brand);
        $this->em->flush();
        $this->createBrandDomains($brand, $domains);
        $this->refreshBrandDomains($brand, $brandEditData);
        $this->imageFacade->uploadImage($brand, $brandData->image, null);

        foreach ($domains as $domain) {
            $this->friendlyUrlFacade->createFriendlyUrlForDomain(
                'front_brand_detail',
                $brand->getId(),
                $brand->getName(),
                $domain->getId()
            );
        }
        $this->em->flush();

        return $brand;
    }

    /**
     * @param $brandId
     * @param \Shopsys\FrameworkBundle\Model\Product\Brand\BrandEditData $brandEditData
     * @return \Shopsys\FrameworkBundle\Model\Product\Brand\Brand
     */
    public function edit($brandId, BrandEditData $brandEditData)
    {
        $domains = $this->domain->getAll();
        $brand = $this->brandRepository->getById($brandId);
        $brandData = $brandEditData->getBrandData();
        $brand->edit($brandData);
        $this->imageFacade->uploadImage($brand, $brandData->image, null);

        $this->refreshBrandDomains($brand, $brandEditData);

        $this->em->flush();

        $this->friendlyUrlFacade->saveUrlListFormData('front_brand_detail', $brand->getId(), $brandData->urls);
        foreach ($domains as $domain) {
            $this->friendlyUrlFacade->createFriendlyUrlForDomain(
                'front_brand_detail',
                $brand->getId(),
                $brand->getName(),
                $domain->getId()
            );
        }
        $this->em->flush();

        return $brand;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Brand\Brand $brand
     * @param \Shopsys\FrameworkBundle\Model\Product\Brand\BrandEditData $brandEditData
     */
    private function refreshBrandDomains(Brand $brand, BrandEditData $brandEditData)
    {
        $brandDomains = $this->brandRepository->getBrandDomainsByBrand($brand);
        $seoTitles = $brandEditData->seoTitles;
        $seoMetaDescriptions = $brandEditData->seoMetaDescriptions;
        $seoH1S = $brandEditData->seoH1s;

        foreach ($brandDomains as $brandDomain) {
            $domainId = $brandDomain->getDomainId();

            if (isset($seoTitles[$domainId])) {
                $brandDomain->setSeoTitle($seoTitles[$domainId]);
            }

            if (!empty($seoMetaDescriptions[$domainId])) {
                $brandDomain->setSeoMetaDescription($seoMetaDescriptions[$domainId]);
            }

            if (!empty($seoH1S[$domainId])) {
                $brandDomain->setSeoH1($seoH1S[$domainId]);
            }
        }

        $this->em->flush($brandDomains);
    }

    /**
     * @param int $brandId
     */
    public function deleteById($brandId)
    {
        $brand = $this->brandRepository->getById($brandId);
        $this->em->remove($brand);
        $this->em->flush();
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Product\Brand\Brand[]
     */
    public function getAll()
    {
        return $this->brandRepository->getAll();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Brand\Brand $brand
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig[] $domains
     */
    private function createBrandDomains(Brand $brand, array $domains)
    {
        $toFlush = [];
        foreach ($domains as $domain) {
            $brandDomain = new BrandDomain($brand, $domain->getId());
            $this->em->persist($brandDomain);
            $toFlush[] = $brandDomain;
        }
        $this->em->flush($toFlush);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Brand\Brand $brand
     * @return \Shopsys\FrameworkBundle\Model\Product\Brand\BrandDomain[]
     */
    public function getBrandDomainsByBrand(Brand $brand)
    {
        return $this->brandRepository->getBrandDomainsByBrand($brand);
    }
}
