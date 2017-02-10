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

    public function __construct(
        EntityManager $em,
        BrandRepository $brandRepository,
        ImageFacade $imageFacade,
        FriendlyUrlFacade $friendlyUrlFacade
    ) {
        $this->em = $em;
        $this->brandRepository = $brandRepository;
        $this->imageFacade = $imageFacade;
        $this->friendlyUrlFacade = $friendlyUrlFacade;
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
     * @param \Shopsys\ShopBundle\Model\Product\Brand\BrandData $brandData
     * @return \Shopsys\ShopBundle\Model\Product\Brand\Brand
     */
    public function create(BrandData $brandData)
    {
        $brand = new Brand($brandData);
        $this->em->persist($brand);
        $this->em->flush();
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
     * @param int $brandId
     * @param \Shopsys\ShopBundle\Model\Product\Brand\BrandData $brandData
     * @return \Shopsys\ShopBundle\Model\Product\Brand\Brand
     */
    public function edit($brandId, BrandData $brandData)
    {
        $brand = $this->brandRepository->getById($brandId);
        $brand->edit($brandData);
        $this->imageFacade->uploadImage($brand, $brandData->image, null);
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
}
