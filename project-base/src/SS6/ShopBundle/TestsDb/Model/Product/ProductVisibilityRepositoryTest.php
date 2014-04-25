<?php

namespace SS6\ShopBundle\TestsDb\Model\Product;

use DateTime;
use SS6\ShopBundle\Component\Test\DatabaseTestCase;
use SS6\ShopBundle\Model\Product\Product;

class ProductVisibilityRepositoryTest extends DatabaseTestCase {
	public function testIsVisibleWhenHidden() {
		$em = $this->getEntityManager();
		
		$hidden = true;
		
		$product = new Product('Name', null, null, null, null, null, null, null, null, $hidden);
		
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
		
		$hidden = false;
		$sellingFrom = new DateTime('now');
		$sellingFrom->modify('+1 day');
		
		$product = new Product('Name', null, null, null, null, null, $sellingFrom, null, null, $hidden);
		
		$em->persist($product);
		$em->flush();
		$id = $product->getId();
		$em->clear();
		
		$productVisibilityRepository = $this->getContainer()->get('ss6.shop.product.product_visibility_repository');
		/* @var $productVisibilityRepository \SS6\ShopBundle\Model\Product\ProductVisibilityRepository */
		$productVisibilityRepository->refreshProductsVisibility();
		
		$productAgain = $em->getRepository(Product::class)->find($id);
		/* @var $productAgain \SS6\ShopBundle\Model\Product\Product */
		
		$this->assertFalse($productAgain->isVisible());
	}
	
	public function testIsVisibleWhenSellingInPast() {
		$em = $this->getEntityManager();
		
		$hidden = false;
		$sellingTo = new DateTime('now');
		$sellingTo->modify('-1 day');
		
		$product = new Product('Name', null, null, null, null, null, null, $sellingTo, null, $hidden);
		
		$em->persist($product);
		$em->flush();
		$id = $product->getId();
		$em->clear();
		
		$productVisibilityRepository = $this->getContainer()->get('ss6.shop.product.product_visibility_repository');
		/* @var $productVisibilityRepository \SS6\ShopBundle\Model\Product\ProductVisibilityRepository */
		$productVisibilityRepository->refreshProductsVisibility();
		
		$productAgain = $em->getRepository(Product::class)->find($id);
		/* @var $productAgain \SS6\ShopBundle\Model\Product\Product */
		
		$this->assertFalse($productAgain->isVisible());
	}
	
	public function testIsVisibleWhenSellingNow() {
		$em = $this->getEntityManager();
		
		$hidden = false;
		$sellingFrom = new DateTime('now');
		$sellingFrom->modify('-1 day');
		$sellingTo = new DateTime('now');
		$sellingTo->modify('+1 day');
		
		$product = new Product('Name', null, null, null, null, null, $sellingFrom, $sellingTo, null, $hidden);
		
		$em->persist($product);
		$em->flush();
		$id = $product->getId();
		$em->clear();
		
		$productVisibilityRepository = $this->getContainer()->get('ss6.shop.product.product_visibility_repository');
		/* @var $productVisibilityRepository \SS6\ShopBundle\Model\Product\ProductVisibilityRepository */
		$productVisibilityRepository->refreshProductsVisibility();
		
		$productAgain = $em->getRepository(Product::class)->find($id);
		/* @var $productAgain \SS6\ShopBundle\Model\Product\Product */
		
		$this->assertTrue($productAgain->isVisible());
	}
}
