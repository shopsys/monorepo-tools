<?php

namespace SS6\ShopBundle\Tests\Database\Model\Feed\HeurekaDelivery;

use SS6\ShopBundle\Component\Domain\Domain;
use SS6\ShopBundle\DataFixtures\Demo\ProductDataFixture;
use SS6\ShopBundle\Model\Product\ProductEditDataFactory;
use SS6\ShopBundle\Model\Product\ProductEditFacade;
use SS6\ShopBundle\Tests\Test\DatabaseTestCase;

/**
 * @UglyTest
 */
class HeurekaDeliveryItemRepositoryTest extends DatabaseTestCase {

	public function testGetItemsWithProductInStock() {
		$container = $this->getContainer();
		$productEditDataFactory = $container->get(ProductEditDataFactory::class);
		/* @var $productEditDataFactory \SS6\ShopBundle\Model\Product\ProductEditDataFactory */
		$productEditFacade = $container->get(ProductEditFacade::class);
		/* @var $productEditFacade \SS6\ShopBundle\Model\Product\ProductEditFacade */
		$domain = $container->get(Domain::class);
		/* @var $domain \SS6\ShopBundle\Component\Domain\Domain */

		$product = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '1');
		/* @var $product \SS6\ShopBundle\Model\Product\Product */

		$productEditData = $productEditDataFactory->createFromProduct($product);
		$productEditData->productData->usingStock = true;
		$productEditData->productData->stockQuantity = 1;
		$productEditFacade->edit($product->getId(), $productEditData);

		$heurekaDeliveryItemRepository = $container->get('ss6.shop.feed.heureka.heureka_delivery_item_repository');
		/* @var $heurekaDeliveryItemRepository \SS6\ShopBundle\Model\Feed\HeurekaDelivery\HeurekaDeliveryItemRepository */
		$seekItemId = null;
		$maxResults = PHP_INT_MAX;
		$heurekaDeliveryItems = $heurekaDeliveryItemRepository->getItems($domain->getCurrentDomainConfig(), $seekItemId, $maxResults);

		foreach ($heurekaDeliveryItems as $heurekaDeliveryItem) {
			/* @var $heurekaDeliveryItem \SS6\ShopBundle\Model\Feed\HeurekaDelivery\HeurekaDeliveryItem*/
			if ($heurekaDeliveryItem->getItemId() == $product->getId()) {
				return;
			}
		}

		$this->fail('Sellable product using stock in stock must be in XML heureka delivery feed.');
	}

	public function testGetItemsWithProductOutOfStock() {
		$container = $this->getContainer();
		$productEditDataFactory = $container->get(ProductEditDataFactory::class);
		/* @var $productEditDataFactory \SS6\ShopBundle\Model\Product\ProductEditDataFactory */
		$productEditFacade = $container->get(ProductEditFacade::class);
		/* @var $productEditFacade \SS6\ShopBundle\Model\Product\ProductEditFacade */
		$domain = $container->get(Domain::class);
		/* @var $domain \SS6\ShopBundle\Component\Domain\Domain */

		$product = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '1');
		/* @var $product \SS6\ShopBundle\Model\Product\Product */

		$productEditData = $productEditDataFactory->createFromProduct($product);
		$productEditData->productData->usingStock = true;
		$productEditData->productData->stockQuantity = 0;
		$productEditFacade->edit($product->getId(), $productEditData);

		$heurekaDeliveryItemRepository = $container->get('ss6.shop.feed.heureka.heureka_delivery_item_repository');
		/* @var $heurekaDeliveryItemRepository \SS6\ShopBundle\Model\Feed\HeurekaDelivery\HeurekaDeliveryItemRepository */
		$seekItemId = null;
		$maxResults = PHP_INT_MAX;
		$heurekaDeliveryItems = $heurekaDeliveryItemRepository->getItems($domain->getCurrentDomainConfig(), $seekItemId, $maxResults);

		foreach ($heurekaDeliveryItems as $heurekaDeliveryItem) {
			/* @var $heurekaDeliveryItem \SS6\ShopBundle\Model\Feed\HeurekaDelivery\HeurekaDeliveryItem*/
			if ($heurekaDeliveryItem->getItemId() == $product->getId()) {
				$this->fail('Sellable product out of stock can not be in XML heureka delivery feed.');
			}
		}
	}

	public function testGetItemsWithProductWithoutStock() {
		$container = $this->getContainer();
		$productEditDataFactory = $container->get(ProductEditDataFactory::class);
		/* @var $productEditDataFactory \SS6\ShopBundle\Model\Product\ProductEditDataFactory */
		$productEditFacade = $container->get(ProductEditFacade::class);
		/* @var $productEditFacade \SS6\ShopBundle\Model\Product\ProductEditFacade */
		$domain = $container->get(Domain::class);
		/* @var $domain \SS6\ShopBundle\Component\Domain\Domain */

		$product = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '1');
		/* @var $product \SS6\ShopBundle\Model\Product\Product */

		$productEditData = $productEditDataFactory->createFromProduct($product);
		$productEditData->productData->usingStock = false;
		$productEditData->productData->stockQuantity = null;
		$productEditFacade->edit($product->getId(), $productEditData);

		$heurekaDeliveryItemRepository = $container->get('ss6.shop.feed.heureka.heureka_delivery_item_repository');
		/* @var $heurekaDeliveryItemRepository \SS6\ShopBundle\Model\Feed\HeurekaDelivery\HeurekaDeliveryItemRepository */
		$seekItemId = null;
		$maxResults = PHP_INT_MAX;
		$heurekaDeliveryItems = $heurekaDeliveryItemRepository->getItems($domain->getCurrentDomainConfig(), $seekItemId, $maxResults);

		foreach ($heurekaDeliveryItems as $heurekaDeliveryItem) {
			/* @var $heurekaDeliveryItem \SS6\ShopBundle\Model\Feed\HeurekaDelivery\HeurekaDeliveryItem*/
			if ($heurekaDeliveryItem->getItemId() == $product->getId()) {
				$this->fail('Sellable product without stock can not be in XML heureka delivery feed.');
			}
		}
	}
}
