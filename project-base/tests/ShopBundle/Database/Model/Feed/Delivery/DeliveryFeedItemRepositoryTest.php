<?php

namespace Tests\ShopBundle\Database\Model\Feed\Delivery;

use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\DataFixtures\Demo\ProductDataFixture;
use Shopsys\FrameworkBundle\Model\Feed\Delivery\DeliveryFeedItemRepository;
use Shopsys\FrameworkBundle\Model\Product\ProductEditDataFactory;
use Shopsys\FrameworkBundle\Model\Product\ProductFacade;
use Tests\ShopBundle\Test\DatabaseTestCase;

class DeliveryFeedItemRepositoryTest extends DatabaseTestCase
{
    public function testGetItemsWithProductInStock()
    {
        $productEditDataFactory = $this->getServiceByType(ProductEditDataFactory::class);
        /* @var $productEditDataFactory \Shopsys\FrameworkBundle\Model\Product\ProductEditDataFactory */
        $productFacade = $this->getServiceByType(ProductFacade::class);
        /* @var $productFacade \Shopsys\FrameworkBundle\Model\Product\ProductFacade */
        $domain = $this->getServiceByType(Domain::class);
        /* @var $domain \Shopsys\FrameworkBundle\Component\Domain\Domain */

        $product = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '1');
        /* @var $product \Shopsys\FrameworkBundle\Model\Product\Product */

        $productEditData = $productEditDataFactory->createFromProduct($product);
        $productEditData->productData->usingStock = true;
        $productEditData->productData->stockQuantity = 1;
        $productFacade->edit($product->getId(), $productEditData);

        $deliveryFeedItemRepository = $this->getServiceByType(DeliveryFeedItemRepository::class);
        /* @var $deliveryFeedItemRepository \Shopsys\FrameworkBundle\Model\Feed\Delivery\DeliveryFeedItemRepository */
        $seekItemId = null;
        $maxResults = PHP_INT_MAX;
        $deliveryFeedItems = $deliveryFeedItemRepository->getItems($domain->getCurrentDomainConfig(), $seekItemId, $maxResults);

        foreach ($deliveryFeedItems as $deliveryFeedItem) {
            /* @var $deliveryFeedItem \Shopsys\FrameworkBundle\Model\Feed\Delivery\DeliveryFeedItem*/
            if ($deliveryFeedItem->getId() == $product->getId()) {
                return;
            }
        }

        $this->fail('Sellable product using stock in stock must be in XML delivery feed.');
    }

    public function testGetItemsWithProductOutOfStock()
    {
        $productEditDataFactory = $this->getServiceByType(ProductEditDataFactory::class);
        /* @var $productEditDataFactory \Shopsys\FrameworkBundle\Model\Product\ProductEditDataFactory */
        $productFacade = $this->getServiceByType(ProductFacade::class);
        /* @var $productFacade \Shopsys\FrameworkBundle\Model\Product\ProductFacade */
        $domain = $this->getServiceByType(Domain::class);
        /* @var $domain \Shopsys\FrameworkBundle\Component\Domain\Domain */

        $product = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '1');
        /* @var $product \Shopsys\FrameworkBundle\Model\Product\Product */

        $productEditData = $productEditDataFactory->createFromProduct($product);
        $productEditData->productData->usingStock = true;
        $productEditData->productData->stockQuantity = 0;
        $productFacade->edit($product->getId(), $productEditData);

        $deliveryFeedItemRepository = $this->getServiceByType(DeliveryFeedItemRepository::class);
        /* @var $deliveryFeedItemRepository \Shopsys\FrameworkBundle\Model\Feed\Delivery\DeliveryFeedItemRepository */
        $seekItemId = null;
        $maxResults = PHP_INT_MAX;
        $deliveryFeedItems = $deliveryFeedItemRepository->getItems($domain->getCurrentDomainConfig(), $seekItemId, $maxResults);

        foreach ($deliveryFeedItems as $deliveryFeedItem) {
            /* @var $deliveryFeedItem \Shopsys\FrameworkBundle\Model\Feed\Delivery\DeliveryFeedItem*/
            if ($deliveryFeedItem->getId() == $product->getId()) {
                $this->fail('Sellable product out of stock can not be in XML delivery feed.');
            }
        }
    }

    public function testGetItemsWithProductWithoutStock()
    {
        $productEditDataFactory = $this->getServiceByType(ProductEditDataFactory::class);
        /* @var $productEditDataFactory \Shopsys\FrameworkBundle\Model\Product\ProductEditDataFactory */
        $productFacade = $this->getServiceByType(ProductFacade::class);
        /* @var $productFacade \Shopsys\FrameworkBundle\Model\Product\ProductFacade */
        $domain = $this->getServiceByType(Domain::class);
        /* @var $domain \Shopsys\FrameworkBundle\Component\Domain\Domain */

        $product = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '1');
        /* @var $product \Shopsys\FrameworkBundle\Model\Product\Product */

        $productEditData = $productEditDataFactory->createFromProduct($product);
        $productEditData->productData->usingStock = false;
        $productEditData->productData->stockQuantity = null;
        $productFacade->edit($product->getId(), $productEditData);

        $deliveryFeedItemRepository = $this->getServiceByType(DeliveryFeedItemRepository::class);
        /* @var $deliveryFeedItemRepository \Shopsys\FrameworkBundle\Model\Feed\Delivery\DeliveryFeedItemRepository */
        $seekItemId = null;
        $maxResults = PHP_INT_MAX;
        $deliveryFeedItems = $deliveryFeedItemRepository->getItems($domain->getCurrentDomainConfig(), $seekItemId, $maxResults);

        foreach ($deliveryFeedItems as $deliveryFeedItem) {
            /* @var $deliveryFeedItem \Shopsys\FrameworkBundle\Model\Feed\Delivery\DeliveryFeedItem*/
            if ($deliveryFeedItem->getId() == $product->getId()) {
                $this->fail('Sellable product without stock can not be in XML delivery feed.');
            }
        }
    }
}
