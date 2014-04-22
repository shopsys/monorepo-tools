<?php

namespace SS6\ShopBundle\TestsDb\Model\Product;

use DateTime;
use SS6\ShopBundle\Component\Test\DatabaseTestCase;
use SS6\ShopBundle\Model\Product\Product;

class ProductVisibilityRepositoryTest extends DatabaseTestCase {
	public function testIsVisibleWhenHidden() {
		$em = $this->getEntityManager();
				
		$product = new Product();
		$product->setName('Name');
		$product->setHidden(true);
		
		$em->persist($product);
		$em->flush();
		$em->clear();
		
		$productVisibilityRepository = $this->getContainer()->get('ss6.shop.product.product_visibility_repository');
		/* @var $productVisibilityRepository \SS6\ShopBundle\Model\Product\ProductVisibilityRepository */
		$productVisibilityRepository->refreshProductsVisibility();
		
		$id = $product->getId();
		$productAgain = $em->getRepository(Product::class)->find($id);
		/* @var $productAgain \SS6\ShopBundle\Model\Product\Product */
		
		$this->assertFalse($productAgain->isVisible());
	}
	
	public function testIsVisibleWhenSellingInFuture() {
		$em = $this->getEntityManager();
		
		$sellingFrom = new DateTime('now');
		$sellingFrom->modify('+1 day');
		
		$product = new Product();
		$product->setName('Name');
		$product->setHidden(false);
		$product->setSellingFrom($sellingFrom);
		
		$em->persist($product);
		$em->flush();
		$em->clear();
		
		$productVisibilityRepository = $this->getContainer()->get('ss6.shop.product.product_visibility_repository');
		/* @var $productVisibilityRepository \SS6\ShopBundle\Model\Product\ProductVisibilityRepository */
		$productVisibilityRepository->refreshProductsVisibility();
		
		$id = $product->getId();
		$productAgain = $em->getRepository(Product::class)->find($id);
		/* @var $productAgain \SS6\ShopBundle\Model\Product\Product */
		
		$this->assertFalse($productAgain->isVisible());
	}
	
	public function testIsVisibleWhenSellingInPast() {
		$em = $this->getEntityManager();
		
		$sellingTo = new DateTime('now');
		$sellingTo->modify('-1 day');
		
		$product = new Product();
		$product->setName('Name');
		$product->setHidden(false);
		$product->setSellingTo($sellingTo);
		
		$em->persist($product);
		$em->flush();
		$em->clear();
		
		$productVisibilityRepository = $this->getContainer()->get('ss6.shop.product.product_visibility_repository');
		/* @var $productVisibilityRepository \SS6\ShopBundle\Model\Product\ProductVisibilityRepository */
		$productVisibilityRepository->refreshProductsVisibility();
		
		$id = $product->getId();
		$productAgain = $em->getRepository(Product::class)->find($id);
		/* @var $productAgain \SS6\ShopBundle\Model\Product\Product */
		
		$this->assertFalse($productAgain->isVisible());
	}
	
	public function testIsVisibleWhenSellingNow() {
		$em = $this->getEntityManager();
		
		$sellingFrom = new DateTime('now');
		$sellingFrom->modify('-1 day');
		$sellingTo = new DateTime('now');
		$sellingTo->modify('+1 day');
		
		$product = new Product();
		$product->setName('Name');
		$product->setHidden(false);
		$product->setSellingFrom($sellingFrom);
		$product->setSellingTo($sellingTo);
		
		$em->persist($product);
		$em->flush();
		$em->clear();
		
		$productVisibilityRepository = $this->getContainer()->get('ss6.shop.product.product_visibility_repository');
		/* @var $productVisibilityRepository \SS6\ShopBundle\Model\Product\ProductVisibilityRepository */
		$productVisibilityRepository->refreshProductsVisibility();
		
		$id = $product->getId();
		$productAgain = $em->getRepository(Product::class)->find($id);
		/* @var $productAgain \SS6\ShopBundle\Model\Product\Product */
		
		$this->assertTrue($productAgain->isVisible());
	}
}
