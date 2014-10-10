<?php

namespace SS6\ShopBundle\TestsDb\Model\Product;

use DateTime;
use SS6\ShopBundle\Component\Test\DatabaseTestCase;
use SS6\ShopBundle\Model\Pricing\Vat\Vat;
use SS6\ShopBundle\Model\Pricing\Vat\VatData;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\ProductData;

class ProductVisibilityRepositoryTest extends DatabaseTestCase {
	public function testIsVisibleWhenHidden() {
		$em = $this->getEntityManager();
		
		$hidden = true;

		$vat = new Vat(new VatData('vat', 21));
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

		$vat = new Vat(new VatData('vat', 21));
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

		$vat = new Vat(new VatData('vat', 21));
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

		$vat = new Vat(new VatData('vat', 21));
		$product = new Product(new ProductData('Name', null, null, null, null, 250, $vat, $sellingFrom, $sellingTo, null, $hidden));

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

	public function testIsNotVisibleWhenZeroOrNullPrice() {
		$em = $this->getEntityManager();

		$hidden = false;
		$vat = new Vat(new VatData('vat', 21));
		$product1 = new Product(new ProductData('Name', null, null, null, null, null, $vat, null, null, null, $hidden));
		$product2 = new Product(new ProductData('Name', null, null, null, null, 0, $vat, null, null, null, $hidden));

		$em->persist($vat);
		$em->persist($product1);
		$em->persist($product2);
		$em->flush();
		$product1Id = $product1->getId();
		$product2Id = $product2->getId();
		$em->clear();

		$productVisibilityRepository = $this->getContainer()->get('ss6.shop.product.product_visibility_repository');
		/* @var $productVisibilityRepository \SS6\ShopBundle\Model\Product\ProductVisibilityRepository */
		$productVisibilityRepository->refreshProductsVisibility();

		$product1Again = $em->getRepository(Product::class)->find($product1Id);
		/* @var $product1Again \SS6\ShopBundle\Model\Product\Product */
		$product2Again = $em->getRepository(Product::class)->find($product2Id);
		/* @var $product2Again \SS6\ShopBundle\Model\Product\Product */

		$this->assertFalse($product1Again->isVisible());
		$this->assertFalse($product2Again->isVisible());
	}
}
