<?php

namespace Shopsys\FrameworkBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\FrameworkBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\FrameworkBundle\Component\DataFixture\PersistentReferenceFacade;
use Shopsys\FrameworkBundle\Component\DataFixture\ProductDataFixtureReferenceInjector;
use Shopsys\FrameworkBundle\Model\Product\ProductEditData;
use Shopsys\FrameworkBundle\Model\Product\ProductFacade;
use Shopsys\FrameworkBundle\Model\Product\ProductVariantFacade;

class ProductDataFixture extends AbstractReferenceFixture implements DependentFixtureInterface
{
    const PRODUCT_PREFIX = 'product_';

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $productDataFixtureLoader = $this->get(ProductDataFixtureLoader::class);
        /* @var $productDataFixtureLoader \Shopsys\FrameworkBundle\DataFixtures\Demo\ProductDataFixtureLoader */
        $referenceInjector = $this->get(ProductDataFixtureReferenceInjector::class);
        /* @var $referenceInjector \Shopsys\FrameworkBundle\Component\DataFixture\ProductDataFixtureReferenceInjector */
        $persistentReferenceFacade = $this->get(PersistentReferenceFacade::class);
        /* @var $persistentReferenceFacade \Shopsys\FrameworkBundle\Component\DataFixture\PersistentReferenceFacade */
        $productDataFixtureCsvReader = $this->get(ProductDataFixtureCsvReader::class);
        /* @var $productDataFixtureCsvReader \Shopsys\FrameworkBundle\DataFixtures\Demo\ProductDataFixtureCsvReader */

        $onlyForFirstDomain = true;
        $referenceInjector->loadReferences($productDataFixtureLoader, $persistentReferenceFacade, $onlyForFirstDomain);

        $csvRows = $productDataFixtureCsvReader->getProductDataFixtureCsvRows();
        $productNo = 1;
        $productsByCatnum = [];
        foreach ($csvRows as $row) {
            $productEditData = $productDataFixtureLoader->createProductEditDataFromRowForFirstDomain($row);
            $product = $this->createProduct(self::PRODUCT_PREFIX . $productNo, $productEditData);

            if ($product->getCatnum() !== null) {
                $productsByCatnum[$product->getCatnum()] = $product;
            }
            $productNo++;
        }

        $this->createVariants($productsByCatnum, $productNo);
    }

    /**
     * @param string $referenceName
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductEditData $productEditData
     * @return \Shopsys\FrameworkBundle\Model\Product\Product
     */
    private function createProduct($referenceName, ProductEditData $productEditData)
    {
        $productFacade = $this->get(ProductFacade::class);
        /* @var $productFacade \Shopsys\FrameworkBundle\Model\Product\ProductFacade */

        $product = $productFacade->create($productEditData);

        $this->addReference($referenceName, $product);

        return $product;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product[] $productsByCatnum
     * @param int $productNo
     */
    private function createVariants(array $productsByCatnum, $productNo)
    {
        $loaderService = $this->get(ProductDataFixtureLoader::class);
        /* @var $loaderService \Shopsys\FrameworkBundle\DataFixtures\Demo\ProductDataFixtureLoader */
        $productVariantFacade = $this->get(ProductVariantFacade::class);
        /* @var $productVariantFacade \Shopsys\FrameworkBundle\Model\Product\ProductVariantFacade */
        $productDataFixtureCsvReader = $this->get(ProductDataFixtureCsvReader::class);
        /* @var $productDataFixtureCsvReader \Shopsys\FrameworkBundle\DataFixtures\Demo\ProductDataFixtureCsvReader */

        $csvRows = $productDataFixtureCsvReader->getProductDataFixtureCsvRows();
        $variantCatnumsByMainVariantCatnum = $loaderService->getVariantCatnumsIndexedByMainVariantCatnum($csvRows);

        foreach ($variantCatnumsByMainVariantCatnum as $mainVariantCatnum => $variantsCatnums) {
            $mainProduct = $productsByCatnum[$mainVariantCatnum];
            /* @var $mainProduct \Shopsys\FrameworkBundle\Model\Product\Product */

            $variants = [];
            foreach ($variantsCatnums as $variantCatnum) {
                $variants[] = $productsByCatnum[$variantCatnum];
            }

            $mainVariant = $productVariantFacade->createVariant($mainProduct, $variants);
            $this->addReference(self::PRODUCT_PREFIX . $productNo, $mainVariant);
            $productNo++;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getDependencies()
    {
        return ProductDataFixtureReferenceInjector::getDependenciesForFirstDomain();
    }
}
