<?php

namespace Shopsys\ShopBundle\DataFixtures\DemoMultidomain;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\ShopBundle\Component\DataFixture\PersistentReferenceFacade;
use Shopsys\ShopBundle\Component\DataFixture\ProductDataFixtureReferenceInjector;
use Shopsys\ShopBundle\DataFixtures\Demo\ProductDataFixtureCsvReader;
use Shopsys\ShopBundle\DataFixtures\Demo\ProductDataFixtureLoader;
use Shopsys\ShopBundle\Model\Product\Product;
use Shopsys\ShopBundle\Model\Product\ProductEditData;
use Shopsys\ShopBundle\Model\Product\ProductEditDataFactory;
use Shopsys\ShopBundle\Model\Product\ProductFacade;

class ProductDataFixture extends AbstractReferenceFixture implements DependentFixtureInterface
{
    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $productDataFixtureLoader = $this->get(ProductDataFixtureLoader::class);
        /* @var $productDataFixtureLoader \Shopsys\ShopBundle\DataFixtures\Demo\ProductDataFixtureLoader */
        $referenceInjector = $this->get(ProductDataFixtureReferenceInjector::class);
        /* @var $referenceInjector \Shopsys\ShopBundle\Component\DataFixture\ProductDataFixtureReferenceInjector */
        $persistentReferenceFacade = $this->get(PersistentReferenceFacade::class);
        /* @var $persistentReferenceFacade \Shopsys\ShopBundle\Component\DataFixture\PersistentReferenceFacade */
        $productDataFixtureCsvReader = $this->get(ProductDataFixtureCsvReader::class);
        /* @var $productDataFixtureCsvReader \Shopsys\ShopBundle\DataFixtures\Demo\ProductDataFixtureCsvReader */
        $productFacade = $this->get(ProductFacade::class);
        /* @var $productFacade \Shopsys\ShopBundle\Model\Product\ProductFacade */

        $onlyForFirstDomain = false;
        $referenceInjector->loadReferences($productDataFixtureLoader, $persistentReferenceFacade, $onlyForFirstDomain);

        $csvRows = $productDataFixtureCsvReader->getProductDataFixtureCsvRows();
        foreach ($csvRows as $row) {
            $productCatnum = $productDataFixtureLoader->getCatnumFromRow($row);
            $product = $productFacade->getOneByCatnumExcludeMainVariants($productCatnum);
            $this->editProduct($product, $row);

            if ($product->isVariant() && $product->getCatnum() === $product->getMainVariant()->getCatnum()) {
                $this->editProduct($product->getMainVariant(), $row);
            }
        }
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Product $product
     * @param array $row
     */
    private function editProduct(Product $product, array $row)
    {
        $productEditDataFactory = $this->get(ProductEditDataFactory::class);
        /* @var $productEditDataFactory \Shopsys\ShopBundle\Model\Product\ProductEditDataFactory */
        $productFacade = $this->get(ProductFacade::class);
        /* @var $productFacade \Shopsys\ShopBundle\Model\Product\ProductFacade */
        $productDataFixtureLoader = $this->get(ProductDataFixtureLoader::class);
        /* @var $productDataFixtureLoader \Shopsys\ShopBundle\DataFixtures\Demo\ProductDataFixtureLoader */

        $productEditData = $productEditDataFactory->createFromProduct($product);
        $productDataFixtureLoader->updateProductEditDataFromCsvRowForSecondDomain($productEditData, $row);
        $productFacade->edit($product->getId(), $productEditData);
    }

    /**
     * {@inheritDoc}
     */
    public function getDependencies()
    {
        return ProductDataFixtureReferenceInjector::getDependenciesForMultidomain();
    }
}
