<?php

namespace Shopsys\ShopBundle\Model\Product\Brand;

use Doctrine\ORM\EntityManager;
use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Component\Image\ImageFacade;
use Shopsys\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlFacade;
use Shopsys\ShopBundle\Model\Product\Brand\Brand;
use Shopsys\ShopBundle\Model\Product\Brand\BrandData;
use Shopsys\ShopBundle\Model\Product\Brand\BrandRepository;

class BrandFacade
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Brand\BrandRepository
     */
    private $brandRepository;

    /**
     * @var \Shopsys\ShopBundle\Component\Image\ImageFacade
     */
    private $imageFacade;

    /**
     * @var \Shopsys\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlFacade
     */
    private $friendlyUrlFacade;

    /**
     * @var \Shopsys\ShopBundle\Component\Domain\Domain
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
     * @return \Shopsys\ShopBundle\Model\Product\Brand\Brand
     */
    public function getById($brandId)
    {
        return $this->brandRepository->getById($brandId);
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Brand\BrandEditData $brandEditData
     * @return \Shopsys\ShopBundle\Model\Product\Brand\Brand
     */
    public function create(BrandEditData $brandEditData)
    {
        $brandData = $brandEditData->getBrandData();
        $brand = new Brand($brandData);
        $this->em->persist($brand);
        $this->em->flush();
        $this->createBrandDomains($brand, $this->domain->getAll());
        $this->refreshBrandDomains($brand, $brandEditData);
        $this->imageFacade->uploadImage($brand, $brandData->image, null);

        $this->friendlyUrlFacade->createFriendlyUrlForDomain(
            'front_brand_detail',
            $brand->getId(),
            $brand->getName(),
            Domain::FIRST_DOMAIN_ID
        );
        $this->em->flush();

        return $brand;
    }

    /**
     * @param $brandId
     * @param \Shopsys\ShopBundle\Model\Product\Brand\BrandEditData $brandEditData
     * @return \Shopsys\ShopBundle\Model\Product\Brand\Brand
     */
    public function edit($brandId, BrandEditData $brandEditData)
    {
        $brand = $this->brandRepository->getById($brandId);
        $brandData = $brandEditData->getBrandData();
        $brand->edit($brandData);
        $this->imageFacade->uploadImage($brand, $brandData->image, null);

        $this->refreshBrandDomains($brand, $brandEditData);

        $this->em->flush();

        $this->friendlyUrlFacade->saveUrlListFormData('front_brand_detail', $brand->getId(), $brandData->urls);
        $this->friendlyUrlFacade->createFriendlyUrlForDomain(
            'front_brand_detail',
            $brand->getId(),
            $brand->getName(),
            Domain::FIRST_DOMAIN_ID
        );
        $this->em->flush();

        return $brand;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Brand\Brand $brand
     * @param \Shopsys\ShopBundle\Model\Product\Brand\BrandEditData $brandEditData
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
     * @return \Shopsys\ShopBundle\Model\Product\Brand\Brand[]
     */
    public function getAll()
    {
        return $this->brandRepository->getAll();
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Brand\Brand $brand
     * @param \Shopsys\ShopBundle\Component\Domain\Config\DomainConfig[] $domains
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
     * @param \Shopsys\ShopBundle\Model\Product\Brand\Brand $brand
     * @return \Shopsys\ShopBundle\Model\Product\Brand\BrandDomain[]
     */
    public function getBrandDomainsByBrand(Brand $brand)
    {
        return $this->brandRepository->getBrandDomainsByBrand($brand);
    }
}
