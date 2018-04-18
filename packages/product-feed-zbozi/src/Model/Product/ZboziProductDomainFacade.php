<?php

namespace Shopsys\ProductFeed\ZboziBundle\Model\Product;

use Doctrine\ORM\EntityManagerInterface;
use Shopsys\FrameworkBundle\Model\Product\ProductRepository;

class ZboziProductDomainFacade
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $em;

    /**
     * @var \Shopsys\ProductFeed\ZboziBundle\Model\Product\ZboziProductDomainRepository
     */
    private $zboziProductDomainRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductRepository
     */
    private $productRepository;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface $em
     * @param \Shopsys\ProductFeed\ZboziBundle\Model\Product\ZboziProductDomainRepository $zboziProductDomainRepository
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductRepository $productRepository
     */
    public function __construct(
        EntityManagerInterface $em,
        ZboziProductDomainRepository $zboziProductDomainRepository,
        ProductRepository $productRepository
    ) {
        $this->em = $em;
        $this->zboziProductDomainRepository = $zboziProductDomainRepository;
        $this->productRepository = $productRepository;
    }

    /**
     * @param int $productId
     * @return \Shopsys\ProductFeed\ZboziBundle\Model\Product\ZboziProductDomain[]|null
     */
    public function findByProductId($productId)
    {
        return $this->zboziProductDomainRepository->findByProductId($productId);
    }

    /**
     * @param array $productsIds
     * @param int $domainId
     * @return \Shopsys\ProductFeed\ZboziBundle\Model\Product\ZboziProductDomain[]
     */
    public function getZboziProductDomainsByProductsIdsDomainIdIndexedByProductId($productsIds, $domainId)
    {
        return $this->zboziProductDomainRepository->getZboziProductDomainsByProductsIdsDomainIdIndexedByProductId(
            $productsIds,
            $domainId
        );
    }

    /**
     * @param $productId
     * @param \Shopsys\ProductFeed\ZboziBundle\Model\Product\ZboziProductDomainData[] $zboziProductDomainsData
     */
    public function saveZboziProductDomainsForProductId($productId, array $zboziProductDomainsData)
    {
        $existingZboziProductDomains = $this->zboziProductDomainRepository->findByProductId($productId);

        $this->removeOldZboziProductDomainsForProductId($existingZboziProductDomains, $zboziProductDomainsData);

        foreach ($zboziProductDomainsData as $zboziProductDomainData) {
            $this->saveZboziProductDomain($productId, $zboziProductDomainData);
        }
    }

    /**
     * @param \Shopsys\ProductFeed\ZboziBundle\Model\Product\ZboziProductDomain[] $existingZboziProductDomains
     * @param \Shopsys\ProductFeed\ZboziBundle\Model\Product\ZboziProductDomainData[] $newZboziProductDomainsData
     */
    private function removeOldZboziProductDomainsForProductId(
        array $existingZboziProductDomains,
        array $newZboziProductDomainsData
    ) {
        $domainsIdsWithNewZboziProductDomains = [];
        foreach ($newZboziProductDomainsData as $newZboziProductDomainData) {
            $domainsIdsWithNewZboziProductDomains[$newZboziProductDomainData->domainId] = $newZboziProductDomainData->domainId;
        }

        foreach ($existingZboziProductDomains as $existingZboziProductDomain) {
            if (!array_key_exists($existingZboziProductDomain->getDomainId(), $domainsIdsWithNewZboziProductDomains)) {
                $this->em->remove($existingZboziProductDomain);
            }
        }
    }

    /**
     * @param $productId
     * @param \Shopsys\ProductFeed\ZboziBundle\Model\Product\ZboziProductDomainData $zboziProductDomainData
     */
    public function saveZboziProductDomain($productId, ZboziProductDomainData $zboziProductDomainData)
    {
        $product = $this->productRepository->getById($productId);
        $zboziProductDomainData->product = $product;

        $existingZboziProductDomain = $this->zboziProductDomainRepository->findByProductIdAndDomainId(
            $productId,
            $zboziProductDomainData->domainId
        );

        if ($existingZboziProductDomain !== null) {
            $existingZboziProductDomain->edit($zboziProductDomainData);
        } else {
            $newZboziProductDomain = new ZboziProductDomain($zboziProductDomainData);
            $this->em->persist($newZboziProductDomain);
        }

        $this->em->flush();
    }

    /**
     * @param $productId
     */
    public function delete($productId)
    {
        $zboziProductDomains = $this->zboziProductDomainRepository->findByProductId($productId);

        foreach ($zboziProductDomains as $zboziProductDomain) {
            $this->em->remove($zboziProductDomain);
            $this->em->flush();
        }
    }
}
