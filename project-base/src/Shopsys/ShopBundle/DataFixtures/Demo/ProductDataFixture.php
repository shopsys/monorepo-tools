<?php

namespace Shopsys\ShopBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\FrameworkBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\FrameworkBundle\Component\DataFixture\PersistentReferenceFacade;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Model\Product\ProductData;
use Shopsys\FrameworkBundle\Model\Product\ProductFacade;
use Shopsys\FrameworkBundle\Model\Product\ProductVariantFacade;
use Shopsys\ShopBundle\DataFixtures\ProductDataFixtureReferenceInjector;

class ProductDataFixture extends AbstractReferenceFixture implements DependentFixtureInterface
{
    public const PRODUCT_PREFIX = 'product_';

    /** @var \Shopsys\ShopBundle\DataFixtures\Demo\ProductDataFixtureLoader */
    protected $productDataFixtureLoader;

    /** @var \Shopsys\ShopBundle\DataFixtures\ProductDataFixtureReferenceInjector */
    protected $referenceInjector;

    /** @var \Shopsys\FrameworkBundle\Component\DataFixture\PersistentReferenceFacade */
    protected $persistentReferenceFacade;

    /** @var \Shopsys\ShopBundle\DataFixtures\Demo\ProductDataFixtureCsvReader */
    protected $productDataFixtureCsvReader;

    /** @var \Shopsys\FrameworkBundle\Model\Product\ProductFacade */
    protected $productFacade;

    /** @var \Shopsys\FrameworkBundle\Model\Product\ProductVariantFacade */
    protected $productVariantFacade;

    /**
     * @param \Shopsys\ShopBundle\DataFixtures\Demo\ProductDataFixtureLoader $productDataFixtureLoader
     * @param \Shopsys\ShopBundle\DataFixtures\ProductDataFixtureReferenceInjector $referenceInjector
     * @param \Shopsys\FrameworkBundle\Component\DataFixture\PersistentReferenceFacade $persistentReferenceFacade
     * @param \Shopsys\ShopBundle\DataFixtures\Demo\ProductDataFixtureCsvReader $productDataFixtureCsvReader
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductFacade $productFacade
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductVariantFacade $productVariantFacade
     */
    public function __construct(
        ProductDataFixtureLoader $productDataFixtureLoader,
        ProductDataFixtureReferenceInjector $referenceInjector,
        PersistentReferenceFacade $persistentReferenceFacade,
        ProductDataFixtureCsvReader $productDataFixtureCsvReader,
        ProductFacade $productFacade,
        ProductVariantFacade $productVariantFacade
    ) {
        $this->productDataFixtureLoader = $productDataFixtureLoader;
        $this->referenceInjector = $referenceInjector;
        $this->persistentReferenceFacade = $persistentReferenceFacade;
        $this->productDataFixtureCsvReader = $productDataFixtureCsvReader;
        $this->productFacade = $productFacade;
        $this->productVariantFacade = $productVariantFacade;
    }

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $this->referenceInjector->loadReferences($this->productDataFixtureLoader, $this->persistentReferenceFacade, Domain::FIRST_DOMAIN_ID);

        $csvRows = $this->productDataFixtureCsvReader->getProductDataFixtureCsvRows();
        $productNo = 1;
        $productsByCatnum = [];
        foreach ($csvRows as $row) {
            $productData = $this->productDataFixtureLoader->createProductDataFromRowForFirstDomain($row);
            $product = $this->createProduct(self::PRODUCT_PREFIX . $productNo, $productData);

            if ($product->getCatnum() !== null) {
                $productsByCatnum[$product->getCatnum()] = $product;
            }
            $productNo++;
        }

        $this->createVariants($productsByCatnum, $productNo);
    }

    /**
     * @param string $referenceName
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductData $productData
     * @return \Shopsys\FrameworkBundle\Model\Product\Product
     */
    protected function createProduct($referenceName, ProductData $productData)
    {
        $product = $this->productFacade->create($productData);

        $this->addReference($referenceName, $product);

        return $product;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product[] $productsByCatnum
     * @param int $productNo
     */
    protected function createVariants(array $productsByCatnum, $productNo)
    {
        $csvRows = $this->productDataFixtureCsvReader->getProductDataFixtureCsvRows();
        $variantCatnumsByMainVariantCatnum = $this->productDataFixtureLoader->getVariantCatnumsIndexedByMainVariantCatnum($csvRows);

        foreach ($variantCatnumsByMainVariantCatnum as $mainVariantCatnum => $variantsCatnums) {
            $mainProduct = $productsByCatnum[$mainVariantCatnum];
            /* @var $mainProduct \Shopsys\FrameworkBundle\Model\Product\Product */

            $variants = [];
            foreach ($variantsCatnums as $variantCatnum) {
                $variants[] = $productsByCatnum[$variantCatnum];
            }

            $mainVariant = $this->productVariantFacade->createVariant($mainProduct, $variants);
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
