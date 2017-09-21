<?php

namespace Shopsys\ShopBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\ShopBundle\Component\DataFixture\ProductDataFixtureReferenceInjector;
use Shopsys\ShopBundle\Model\Product\ProductEditData;

class ProductDataFixture extends AbstractReferenceFixture implements DependentFixtureInterface
{
    const PRODUCT_PREFIX = 'product_';

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $productDataFixtureLoader = $this->get('shopsys.shop.data_fixtures.demo.product_data_fixture_loader');
        /* @var $productDataFixtureLoader \Shopsys\ShopBundle\DataFixtures\Demo\ProductDataFixtureLoader */
        $referenceInjector = $this->get('shopsys.shop.component.data_fixture.product_data_fixture_reference_injector');
        /* @var $referenceInjector \Shopsys\ShopBundle\Component\DataFixture\ProductDataFixtureReferenceInjector */
        $persistentReferenceFacade = $this->get('shopsys.shop.component.data_fixture.persistent_reference_facade');
        /* @var $persistentReferenceFacade \Shopsys\ShopBundle\Component\DataFixture\PersistentReferenceFacade */
        $productDataFixtureCsvReader = $this->get('shopsys.shop.data_fixtures.product_data_fixture_csv_reader');
        /* @var $productDataFixtureCsvReader \Shopsys\ShopBundle\DataFixtures\Demo\ProductDataFixtureCsvReader */

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
     * @param \Shopsys\ShopBundle\Model\Product\ProductEditData $productEditData
     * @return \Shopsys\ShopBundle\Model\Product\Product
     */
    private function createProduct($referenceName, ProductEditData $productEditData)
    {
        $productFacade = $this->get('shopsys.shop.product.product_facade');
        /* @var $productFacade \Shopsys\ShopBundle\Model\Product\ProductFacade */

        $product = $productFacade->create($productEditData);

        $this->addReference($referenceName, $product);

        return $product;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Product[] $productsByCatnum
     * @param int $productNo
     */
    private function createVariants(array $productsByCatnum, $productNo)
    {
        $loaderService = $this->get('shopsys.shop.data_fixtures.demo.product_data_fixture_loader');
        /* @var $loaderService \Shopsys\ShopBundle\DataFixtures\Demo\ProductDataFixtureLoader */
        $productVariantFacade = $this->get('shopsys.shop.product.product_variant_facade');
        /* @var $productVariantFacade \Shopsys\ShopBundle\Model\Product\ProductVariantFacade */
        $productDataFixtureCsvReader = $this->get('shopsys.shop.data_fixtures.product_data_fixture_csv_reader');
        /* @var $productDataFixtureCsvReader \Shopsys\ShopBundle\DataFixtures\Demo\ProductDataFixtureCsvReader */

        $csvRows = $productDataFixtureCsvReader->getProductDataFixtureCsvRows();
        $variantCatnumsByMainVariantCatnum = $loaderService->getVariantCatnumsIndexedByMainVariantCatnum($csvRows);

        foreach ($variantCatnumsByMainVariantCatnum as $mainVariantCatnum => $variantsCatnums) {
            $mainProduct = $productsByCatnum[$mainVariantCatnum];
            /* @var $mainProduct \Shopsys\ShopBundle\Model\Product\Product */

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
