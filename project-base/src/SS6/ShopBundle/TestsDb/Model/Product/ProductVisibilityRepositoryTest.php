<?php

namespace SS6\ShopBundle\TestsDb\Model\Product;

use DateTime;
use SS6\ShopBundle\Component\Test\DatabaseTestCase;
use SS6\ShopBundle\Model\Pricing\Vat\Vat;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\ProductData;

class ProductVisibilityRepositoryTest extends DatabaseTestCase {
	public function testIsVisibleWhenHidden() {
		$em = $this->getEntityManager();
		
		$hidden = true;

		$vat = new Vat('vat', 21);
		$product = new Product(new ProductData('Name', null, null, null, null, null, $vat, null, null, null, $hidden));

		$em->persist($vat);
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

		$vat = new Vat('vat', 21);
		$product = new Product(new ProductData('Name', null, null, null, null, null, $vat, $sellingFrom, null, null, $hidden));

		$em->persist($vat);
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

		$vat = new Vat('vat', 21);
		$product = new Product(new ProductData('Name', null, null, null, null, null, $vat, null, $sellingTo, null, $hidden));

		$em->persist($vat);
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

		$vat = new Vat('vat', 21);
		$product = new Product(new ProductData('Name', null, null, null, null, null, $vat, $sellingFrom, $sellingTo, null, $hidden));

		$em->persist($vat);
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
