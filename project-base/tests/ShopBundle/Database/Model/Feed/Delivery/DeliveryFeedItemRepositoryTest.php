<?php

namespace Tests\ShopBundle\Database\Model\Feed\Delivery;

use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\DataFixtures\Demo\ProductDataFixture;
use Shopsys\FrameworkBundle\Model\Feed\Delivery\DeliveryFeedItemRepository;
use Shopsys\FrameworkBundle\Model\Product\ProductDataFactory;
use Shopsys\FrameworkBundle\Model\Product\ProductFacade;
use Tests\ShopBundle\Test\DatabaseTestCase;

class DeliveryFeedItemRepositoryTest extends DatabaseTestCase
{
    public function testGetItemsWithProductInStock()
    {
        $productDataFactory = $this->getContainer()->get(ProductDataFactory::class);
        /* @var $productDataFactory \Shopsys\FrameworkBundle\Model\Product\ProductDataFactory */
        $productFacade = $this->getContainer()->get(ProductFacade::class);
        /* @var $productFacade \Shopsys\FrameworkBundle\Model\Product\ProductFacade */
        $domain = $this->getContainer()->get(Domain::class);
        /* @var $domain \Shopsys\FrameworkBundle\Component\Domain\Domain */

        $product = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '1');
        /* @var $product \Shopsys\FrameworkBundle\Model\Product\Product */

        $productData = $productDataFactory->createFromProduct($product);
        $productData->usingStock = true;
        $productData->stockQuantity = 1;
        $productFacade->edit($product->getId(), $productData);

        $deliveryFeedItemRepository = $this->getContainer()->get(DeliveryFeedItemRepository::class);
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
        $productDataFactory = $this->getContainer()->get(ProductDataFactory::class);
        /* @var $productDataFactory \Shopsys\FrameworkBundle\Model\Product\ProductDataFactory */
        $productFacade = $this->getContainer()->get(ProductFacade::class);
        /* @var $productFacade \Shopsys\FrameworkBundle\Model\Product\ProductFacade */
        $domain = $this->getContainer()->get(Domain::class);
        /* @var $domain \Shopsys\FrameworkBundle\Component\Domain\Domain */

        $product = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '1');
        /* @var $product \Shopsys\FrameworkBundle\Model\Product\Product */

        $productData = $productDataFactory->createFromProduct($product);
        $productData->usingStock = true;
        $productData->stockQuantity = 0;
        $productFacade->edit($product->getId(), $productData);

        $deliveryFeedItemRepository = $this->getContainer()->get(DeliveryFeedItemRepository::class);
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
        $productDataFactory = $this->getContainer()->get(ProductDataFactory::class);
        /* @var $productDataFactory \Shopsys\FrameworkBundle\Model\Product\ProductDataFactory */
        $productFacade = $this->getContainer()->get(ProductFacade::class);
        /* @var $productFacade \Shopsys\FrameworkBundle\Model\Product\ProductFacade */
        $domain = $this->getContainer()->get(Domain::class);
        /* @var $domain \Shopsys\FrameworkBundle\Component\Domain\Domain */

        $product = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '1');
        /* @var $product \Shopsys\FrameworkBundle\Model\Product\Product */

        $productData = $productDataFactory->createFromProduct($product);
        $productData->usingStock = false;
        $productData->stockQuantity = null;
        $productFacade->edit($product->getId(), $productData);

        $deliveryFeedItemRepository = $this->getContainer()->get(DeliveryFeedItemRepository::class);
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
