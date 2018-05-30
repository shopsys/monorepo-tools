<?php

namespace Shopsys\FrameworkBundle\Model\Product\Brand;

use Doctrine\ORM\EntityManagerInterface;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\Image\ImageFacade;
use Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrlFacade;

class BrandFacade
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $em;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Brand\BrandRepository
     */
    protected $brandRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Image\ImageFacade
     */
    protected $imageFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrlFacade
     */
    protected $friendlyUrlFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    protected $domain;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Brand\BrandFactoryInterface
     */
    protected $brandFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Brand\BrandDomainFactoryInterface
     */
    protected $brandDomainFactory;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface $em
     * @param \Shopsys\FrameworkBundle\Model\Product\Brand\BrandRepository $brandRepository
     * @param \Shopsys\FrameworkBundle\Component\Image\ImageFacade $imageFacade
     * @param \Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrlFacade $friendlyUrlFacade
     * @param \Shopsys\FrameworkBundle\Component\Domain\Domain $domain
     * @param \Shopsys\FrameworkBundle\Model\Product\Brand\BrandFactoryInterface $brandFactory
     * @param \Shopsys\FrameworkBundle\Model\Product\Brand\BrandDomainFactoryInterface $brandDomainFactory
     */
    public function __construct(
        EntityManagerInterface $em,
        BrandRepository $brandRepository,
        ImageFacade $imageFacade,
        FriendlyUrlFacade $friendlyUrlFacade,
        Domain $domain,
        BrandFactoryInterface $brandFactory,
        BrandDomainFactoryInterface $brandDomainFactory
    ) {
        $this->em = $em;
        $this->brandRepository = $brandRepository;
        $this->imageFacade = $imageFacade;
        $this->friendlyUrlFacade = $friendlyUrlFacade;
        $this->domain = $domain;
        $this->brandFactory = $brandFactory;
        $this->brandDomainFactory = $brandDomainFactory;
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
     * @param \Shopsys\FrameworkBundle\Model\Product\Brand\BrandData $brandData
     * @return \Shopsys\FrameworkBundle\Model\Product\Brand\Brand
     */
    public function create(BrandData $brandData)
    {
        $domains = $this->domain->getAll();
        $brand = $this->brandFactory->create($brandData);
        $this->em->persist($brand);
        $this->em->flush();
        $this->createBrandDomains($brand, $domains);
        $this->refreshBrandDomains($brand, $brandData);
        $this->imageFacade->uploadImage($brand, $brandData->image->uploadedFiles, null);

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
     * @param \Shopsys\FrameworkBundle\Model\Product\Brand\BrandData $brandData
     * @return \Shopsys\FrameworkBundle\Model\Product\Brand\Brand
     */
    public function edit($brandId, BrandData $brandData)
    {
        $domains = $this->domain->getAll();
        $brand = $this->brandRepository->getById($brandId);
        $brand->edit($brandData);
        $this->imageFacade->uploadImage($brand, $brandData->image->uploadedFiles, null);

        $this->refreshBrandDomains($brand, $brandData);

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
     * @param \Shopsys\FrameworkBundle\Model\Product\Brand\BrandData $brandData
     */
    protected function refreshBrandDomains(Brand $brand, BrandData $brandData)
    {
        $brandDomains = $this->brandRepository->getBrandDomainsByBrand($brand);
        $seoTitles = $brandData->seoTitles;
        $seoMetaDescriptions = $brandData->seoMetaDescriptions;
        $seoH1S = $brandData->seoH1s;

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
    protected function createBrandDomains(Brand $brand, array $domains)
    {
        $toFlush = [];
        foreach ($domains as $domain) {
            $brandDomain = $this->brandDomainFactory->create($brand, $domain->getId());
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
