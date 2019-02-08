<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\FrameworkBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\FrameworkBundle\Component\DataFixture\PersistentReferenceFacade;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\DataFixtures\ProductDataFixtureReferenceInjector;
use Shopsys\FrameworkBundle\Model\Product\Product;
use Shopsys\FrameworkBundle\Model\Product\ProductDataFactoryInterface;
use Shopsys\FrameworkBundle\Model\Product\ProductFacade;

class MultidomainProductDataFixture extends AbstractReferenceFixture implements DependentFixtureInterface
{
    /**
     * @var \Shopsys\FrameworkBundle\DataFixtures\Demo\ProductDataFixtureLoader
     */
    protected $productDataFixtureLoader;

    /**
     * @var \Shopsys\FrameworkBundle\DataFixtures\ProductDataFixtureReferenceInjector
     */
    protected $referenceInjector;

    /**
     * @var \Shopsys\FrameworkBundle\Component\DataFixture\PersistentReferenceFacade
     */
    protected $persistentReferenceFacade;

    /**
     * @var \Shopsys\FrameworkBundle\DataFixtures\Demo\ProductDataFixtureCsvReader
     */
    protected $productDataFixtureCsvReader;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductFacade
     */
    protected $productFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductDataFactoryInterface
     */
    protected $productDataFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    protected $domain;

    /**
     * @param \Shopsys\FrameworkBundle\DataFixtures\Demo\ProductDataFixtureLoader $productDataFixtureLoader
     * @param \Shopsys\FrameworkBundle\DataFixtures\ProductDataFixtureReferenceInjector $referenceInjector
     * @param \Shopsys\FrameworkBundle\Component\DataFixture\PersistentReferenceFacade $persistentReferenceFacade
     * @param \Shopsys\FrameworkBundle\DataFixtures\Demo\ProductDataFixtureCsvReader $productDataFixtureCsvReader
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductFacade $productFacade
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductDataFactoryInterface $productDataFactory
     * @param \Shopsys\FrameworkBundle\Component\Domain\Domain $domain
     */
    public function __construct(
        ProductDataFixtureLoader $productDataFixtureLoader,
        ProductDataFixtureReferenceInjector $referenceInjector,
        PersistentReferenceFacade $persistentReferenceFacade,
        ProductDataFixtureCsvReader $productDataFixtureCsvReader,
        ProductFacade $productFacade,
        ProductDataFactoryInterface $productDataFactory,
        Domain $domain
    ) {
        $this->productDataFixtureLoader = $productDataFixtureLoader;
        $this->referenceInjector = $referenceInjector;
        $this->persistentReferenceFacade = $persistentReferenceFacade;
        $this->productDataFixtureCsvReader = $productDataFixtureCsvReader;
        $this->productFacade = $productFacade;
        $this->productDataFactory = $productDataFactory;
        $this->domain = $domain;
    }

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        foreach ($this->domain->getAllIdsExcludingFirstDomain() as $domainId) {
            $this->loadForDomain($domainId);
        }
    }

    /**
     * @param int $domainId
     */
    protected function loadForDomain(int $domainId)
    {
        $this->referenceInjector->loadReferences($this->productDataFixtureLoader, $this->persistentReferenceFacade, $domainId);

        $csvRows = $this->productDataFixtureCsvReader->getProductDataFixtureCsvRows();
        foreach ($csvRows as $row) {
            $productCatnum = $this->productDataFixtureLoader->getCatnumFromRow($row);
            $product = $this->productFacade->getOneByCatnumExcludeMainVariants($productCatnum);
            $this->editProduct($product, $row);

            if ($product->isVariant() && $product->getCatnum() === $product->getMainVariant()->getCatnum()) {
                $this->editProduct($product->getMainVariant(), $row);
            }
        }
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param array $row
     */
    protected function editProduct(Product $product, array $row)
    {
        $productData = $this->productDataFactory->createFromProduct($product);
        $this->productDataFixtureLoader->updateProductDataFromCsvRowForSecondDomain($productData, $row);
        $this->productFacade->edit($product->getId(), $productData);
    }

    /**
     * {@inheritDoc}
     */
    public function getDependencies()
    {
        return [
            MultidomainPricingGroupDataFixture::class,
            ProductDataFixture::class,
        ];
    }
}
