<?php

namespace Tests\ShopBundle\Database\Model\Feed\HeurekaDelivery;

use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\DataFixtures\Demo\ProductDataFixture;
use Shopsys\ShopBundle\Model\Feed\HeurekaDelivery\HeurekaDeliveryItemRepository;
use Shopsys\ShopBundle\Model\Product\ProductEditDataFactory;
use Shopsys\ShopBundle\Model\Product\ProductFacade;
use Tests\ShopBundle\Test\DatabaseTestCase;

class HeurekaDeliveryItemRepositoryTest extends DatabaseTestCase
{
    public function testGetItemsWithProductInStock()
    {
        $productEditDataFactory = $this->getServiceByType(ProductEditDataFactory::class);
        /* @var $productEditDataFactory \Shopsys\ShopBundle\Model\Product\ProductEditDataFactory */
        $productFacade = $this->getServiceByType(ProductFacade::class);
        /* @var $productFacade \Shopsys\ShopBundle\Model\Product\ProductFacade */
        $domain = $this->getServiceByType(Domain::class);
        /* @var $domain \Shopsys\ShopBundle\Component\Domain\Domain */

        $product = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '1');
        /* @var $product \Shopsys\ShopBundle\Model\Product\Product */

        $productEditData = $productEditDataFactory->createFromProduct($product);
        $productEditData->productData->usingStock = true;
        $productEditData->productData->stockQuantity = 1;
        $productFacade->edit($product->getId(), $productEditData);

        $heurekaDeliveryItemRepository = $this->getServiceByType(HeurekaDeliveryItemRepository::class);
        /* @var $heurekaDeliveryItemRepository \Shopsys\ShopBundle\Model\Feed\HeurekaDelivery\HeurekaDeliveryItemRepository */
        $seekItemId = null;
        $maxResults = PHP_INT_MAX;
        $heurekaDeliveryItems = $heurekaDeliveryItemRepository->getItems($domain->getCurrentDomainConfig(), $seekItemId, $maxResults);

        foreach ($heurekaDeliveryItems as $heurekaDeliveryItem) {
            /* @var $heurekaDeliveryItem \Shopsys\ShopBundle\Model\Feed\HeurekaDelivery\HeurekaDeliveryItem*/
            if ($heurekaDeliveryItem->getId() == $product->getId()) {
                return;
            }
        }

        $this->fail('Sellable product using stock in stock must be in XML heureka delivery feed.');
    }

    public function testGetItemsWithProductOutOfStock()
    {
        $productEditDataFactory = $this->getServiceByType(ProductEditDataFactory::class);
        /* @var $productEditDataFactory \Shopsys\ShopBundle\Model\Product\ProductEditDataFactory */
        $productFacade = $this->getServiceByType(ProductFacade::class);
        /* @var $productFacade \Shopsys\ShopBundle\Model\Product\ProductFacade */
        $domain = $this->getServiceByType(Domain::class);
        /* @var $domain \Shopsys\ShopBundle\Component\Domain\Domain */

        $product = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '1');
        /* @var $product \Shopsys\ShopBundle\Model\Product\Product */

        $productEditData = $productEditDataFactory->createFromProduct($product);
        $productEditData->productData->usingStock = true;
        $productEditData->productData->stockQuantity = 0;
        $productFacade->edit($product->getId(), $productEditData);

        $heurekaDeliveryItemRepository = $this->getServiceByType(HeurekaDeliveryItemRepository::class);
        /* @var $heurekaDeliveryItemRepository \Shopsys\ShopBundle\Model\Feed\HeurekaDelivery\HeurekaDeliveryItemRepository */
        $seekItemId = null;
        $maxResults = PHP_INT_MAX;
        $heurekaDeliveryItems = $heurekaDeliveryItemRepository->getItems($domain->getCurrentDomainConfig(), $seekItemId, $maxResults);

        foreach ($heurekaDeliveryItems as $heurekaDeliveryItem) {
            /* @var $heurekaDeliveryItem \Shopsys\ShopBundle\Model\Feed\HeurekaDelivery\HeurekaDeliveryItem*/
            if ($heurekaDeliveryItem->getId() == $product->getId()) {
                $this->fail('Sellable product out of stock can not be in XML heureka delivery feed.');
            }
        }
    }

    public function testGetItemsWithProductWithoutStock()
    {
        $productEditDataFactory = $this->getServiceByType(ProductEditDataFactory::class);
        /* @var $productEditDataFactory \Shopsys\ShopBundle\Model\Product\ProductEditDataFactory */
        $productFacade = $this->getServiceByType(ProductFacade::class);
        /* @var $productFacade \Shopsys\ShopBundle\Model\Product\ProductFacade */
        $domain = $this->getServiceByType(Domain::class);
        /* @var $domain \Shopsys\ShopBundle\Component\Domain\Domain */

        $product = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '1');
        /* @var $product \Shopsys\ShopBundle\Model\Product\Product */

        $productEditData = $productEditDataFactory->createFromProduct($product);
        $productEditData->productData->usingStock = false;
        $productEditData->productData->stockQuantity = null;
        $productFacade->edit($product->getId(), $productEditData);

        $heurekaDeliveryItemRepository = $this->getServiceByType(HeurekaDeliveryItemRepository::class);
        /* @var $heurekaDeliveryItemRepository \Shopsys\ShopBundle\Model\Feed\HeurekaDelivery\HeurekaDeliveryItemRepository */
        $seekItemId = null;
        $maxResults = PHP_INT_MAX;
        $heurekaDeliveryItems = $heurekaDeliveryItemRepository->getItems($domain->getCurrentDomainConfig(), $seekItemId, $maxResults);

        foreach ($heurekaDeliveryItems as $heurekaDeliveryItem) {
            /* @var $heurekaDeliveryItem \Shopsys\ShopBundle\Model\Feed\HeurekaDelivery\HeurekaDeliveryItem*/
            if ($heurekaDeliveryItem->getId() == $product->getId()) {
                $this->fail('Sellable product without stock can not be in XML heureka delivery feed.');
            }
        }
    }
}
