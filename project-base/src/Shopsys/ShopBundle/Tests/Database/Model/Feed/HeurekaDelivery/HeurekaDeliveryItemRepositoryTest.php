<?php

namespace Shopsys\ShopBundle\Tests\Database\Model\Feed\HeurekaDelivery;

use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\DataFixtures\Demo\ProductDataFixture;
use Shopsys\ShopBundle\Model\Product\ProductEditDataFactory;
use Shopsys\ShopBundle\Model\Product\ProductFacade;
use Shopsys\ShopBundle\Tests\Test\DatabaseTestCase;

class HeurekaDeliveryItemRepositoryTest extends DatabaseTestCase
{
    public function testGetItemsWithProductInStock() {
        $container = $this->getContainer();
        $productEditDataFactory = $container->get(ProductEditDataFactory::class);
        /* @var $productEditDataFactory \Shopsys\ShopBundle\Model\Product\ProductEditDataFactory */
        $productFacade = $container->get(ProductFacade::class);
        /* @var $productFacade \Shopsys\ShopBundle\Model\Product\ProductFacade */
        $domain = $container->get(Domain::class);
        /* @var $domain \Shopsys\ShopBundle\Component\Domain\Domain */

        $product = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '1');
        /* @var $product \Shopsys\ShopBundle\Model\Product\Product */

        $productEditData = $productEditDataFactory->createFromProduct($product);
        $productEditData->productData->usingStock = true;
        $productEditData->productData->stockQuantity = 1;
        $productFacade->edit($product->getId(), $productEditData);

        $heurekaDeliveryItemRepository = $container->get('shopsys.shop.feed.heureka.heureka_delivery_item_repository');
        /* @var $heurekaDeliveryItemRepository \Shopsys\ShopBundle\Model\Feed\HeurekaDelivery\HeurekaDeliveryItemRepository */
        $seekItemId = null;
        $maxResults = PHP_INT_MAX;
        $heurekaDeliveryItems = $heurekaDeliveryItemRepository->getItems($domain->getCurrentDomainConfig(), $seekItemId, $maxResults);

        foreach ($heurekaDeliveryItems as $heurekaDeliveryItem) {
            /* @var $heurekaDeliveryItem \Shopsys\ShopBundle\Model\Feed\HeurekaDelivery\HeurekaDeliveryItem*/
            if ($heurekaDeliveryItem->getItemId() == $product->getId()) {
                return;
            }
        }

        $this->fail('Sellable product using stock in stock must be in XML heureka delivery feed.');
    }

    public function testGetItemsWithProductOutOfStock() {
        $container = $this->getContainer();
        $productEditDataFactory = $container->get(ProductEditDataFactory::class);
        /* @var $productEditDataFactory \Shopsys\ShopBundle\Model\Product\ProductEditDataFactory */
        $productFacade = $container->get(ProductFacade::class);
        /* @var $productFacade \Shopsys\ShopBundle\Model\Product\ProductFacade */
        $domain = $container->get(Domain::class);
        /* @var $domain \Shopsys\ShopBundle\Component\Domain\Domain */

        $product = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '1');
        /* @var $product \Shopsys\ShopBundle\Model\Product\Product */

        $productEditData = $productEditDataFactory->createFromProduct($product);
        $productEditData->productData->usingStock = true;
        $productEditData->productData->stockQuantity = 0;
        $productFacade->edit($product->getId(), $productEditData);

        $heurekaDeliveryItemRepository = $container->get('shopsys.shop.feed.heureka.heureka_delivery_item_repository');
        /* @var $heurekaDeliveryItemRepository \Shopsys\ShopBundle\Model\Feed\HeurekaDelivery\HeurekaDeliveryItemRepository */
        $seekItemId = null;
        $maxResults = PHP_INT_MAX;
        $heurekaDeliveryItems = $heurekaDeliveryItemRepository->getItems($domain->getCurrentDomainConfig(), $seekItemId, $maxResults);

        foreach ($heurekaDeliveryItems as $heurekaDeliveryItem) {
            /* @var $heurekaDeliveryItem \Shopsys\ShopBundle\Model\Feed\HeurekaDelivery\HeurekaDeliveryItem*/
            if ($heurekaDeliveryItem->getItemId() == $product->getId()) {
                $this->fail('Sellable product out of stock can not be in XML heureka delivery feed.');
            }
        }
    }

    public function testGetItemsWithProductWithoutStock() {
        $container = $this->getContainer();
        $productEditDataFactory = $container->get(ProductEditDataFactory::class);
        /* @var $productEditDataFactory \Shopsys\ShopBundle\Model\Product\ProductEditDataFactory */
        $productFacade = $container->get(ProductFacade::class);
        /* @var $productFacade \Shopsys\ShopBundle\Model\Product\ProductFacade */
        $domain = $container->get(Domain::class);
        /* @var $domain \Shopsys\ShopBundle\Component\Domain\Domain */

        $product = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '1');
        /* @var $product \Shopsys\ShopBundle\Model\Product\Product */

        $productEditData = $productEditDataFactory->createFromProduct($product);
        $productEditData->productData->usingStock = false;
        $productEditData->productData->stockQuantity = null;
        $productFacade->edit($product->getId(), $productEditData);

        $heurekaDeliveryItemRepository = $container->get('shopsys.shop.feed.heureka.heureka_delivery_item_repository');
        /* @var $heurekaDeliveryItemRepository \Shopsys\ShopBundle\Model\Feed\HeurekaDelivery\HeurekaDeliveryItemRepository */
        $seekItemId = null;
        $maxResults = PHP_INT_MAX;
        $heurekaDeliveryItems = $heurekaDeliveryItemRepository->getItems($domain->getCurrentDomainConfig(), $seekItemId, $maxResults);

        foreach ($heurekaDeliveryItems as $heurekaDeliveryItem) {
            /* @var $heurekaDeliveryItem \Shopsys\ShopBundle\Model\Feed\HeurekaDelivery\HeurekaDeliveryItem*/
            if ($heurekaDeliveryItem->getItemId() == $product->getId()) {
                $this->fail('Sellable product without stock can not be in XML heureka delivery feed.');
            }
        }
    }
}
