<?php

namespace Tests\ShopBundle\Database\Model\Feed\Delivery;

use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\DataFixtures\Demo\ProductDataFixture;
use Shopsys\ShopBundle\Model\Feed\Delivery\DeliveryFeedItemRepository;
use Shopsys\ShopBundle\Model\Product\ProductEditDataFactory;
use Shopsys\ShopBundle\Model\Product\ProductFacade;
use Tests\ShopBundle\Test\DatabaseTestCase;

class DeliveryFeedItemRepositoryTest extends DatabaseTestCase
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

        $deliveryFeedItemRepository = $this->getServiceByType(DeliveryFeedItemRepository::class);
        /* @var $deliveryFeedItemRepository \Shopsys\ShopBundle\Model\Feed\Delivery\DeliveryFeedItemRepository */
        $seekItemId = null;
        $maxResults = PHP_INT_MAX;
        $deliveryFeedItems = $deliveryFeedItemRepository->getItems($domain->getCurrentDomainConfig(), $seekItemId, $maxResults);

        foreach ($deliveryFeedItems as $deliveryFeedItem) {
            /* @var $deliveryFeedItem \Shopsys\ShopBundle\Model\Feed\Delivery\DeliveryFeedItem*/
            if ($deliveryFeedItem->getId() == $product->getId()) {
                return;
            }
        }

        $this->fail('Sellable product using stock in stock must be in XML delivery feed.');
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

        $deliveryFeedItemRepository = $this->getServiceByType(DeliveryFeedItemRepository::class);
        /* @var $deliveryFeedItemRepository \Shopsys\ShopBundle\Model\Feed\Delivery\DeliveryFeedItemRepository */
        $seekItemId = null;
        $maxResults = PHP_INT_MAX;
        $deliveryFeedItems = $deliveryFeedItemRepository->getItems($domain->getCurrentDomainConfig(), $seekItemId, $maxResults);

        foreach ($deliveryFeedItems as $deliveryFeedItem) {
            /* @var $deliveryFeedItem \Shopsys\ShopBundle\Model\Feed\Delivery\DeliveryFeedItem*/
            if ($deliveryFeedItem->getId() == $product->getId()) {
                $this->fail('Sellable product out of stock can not be in XML delivery feed.');
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

        $deliveryFeedItemRepository = $this->getServiceByType(DeliveryFeedItemRepository::class);
        /* @var $deliveryFeedItemRepository \Shopsys\ShopBundle\Model\Feed\Delivery\DeliveryFeedItemRepository */
        $seekItemId = null;
        $maxResults = PHP_INT_MAX;
        $deliveryFeedItems = $deliveryFeedItemRepository->getItems($domain->getCurrentDomainConfig(), $seekItemId, $maxResults);

        foreach ($deliveryFeedItems as $deliveryFeedItem) {
            /* @var $deliveryFeedItem \Shopsys\ShopBundle\Model\Feed\Delivery\DeliveryFeedItem*/
            if ($deliveryFeedItem->getId() == $product->getId()) {
                $this->fail('Sellable product without stock can not be in XML delivery feed.');
            }
        }
    }
}
