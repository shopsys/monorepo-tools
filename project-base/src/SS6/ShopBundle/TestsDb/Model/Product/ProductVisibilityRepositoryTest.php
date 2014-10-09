<?php

namespace SS6\ShopBundle\TestsDb\Model\Product;

use DateTime;
use SS6\ShopBundle\Component\Test\DatabaseTestCase;
use SS6\ShopBundle\Model\Pricing\Vat\Vat;
use SS6\ShopBundle\Model\Pricing\Vat\VatData;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\ProductData;

class ProductVisibilityRepositoryTest extends DatabaseTestCase {

	public function testIsVisibleOnAnyDomainWhenHidden() {
		$em = $this->getEntityManager();
		$productEditFacade = $this->getContainer()->get('ss6.shop.product.product_edit_facade');
		
		$vat = new Vat(new VatData('vat', 21));
		$em->persist($vat);

		$productData = new ProductData();
		$productData->setName('Name');
		$productData->setVat($vat);
		$productData->setHidden(array(1 => true));
		$product = $productEditFacade->create($productData);

		$em->flush();
		$id = $product->getId();
		$em->clear();
		
		$productVisibilityRepository = $this->getContainer()->get('ss6.shop.product.product_visibility_repository');
		/* @var $productVisibilityRepository \SS6\ShopBundle\Model\Product\ProductVisibilityRepository */
		$productVisibilityRepository->refreshProductsVisibility();
		
		$productAgain = $em->getRepository(Product::class)->find($id);
		/* @var $productAgain \SS6\ShopBundle\Model\Product\Product */
		
		$this->assertFalse($productAgain->isVisibleOnAnyDomain());
	}
	
	public function testIsVisibleOnAnyDomainWhenSellingInFuture() {
		$em = $this->getEntityManager();
		$productEditFacade = $this->getContainer()->get('ss6.shop.product.product_edit_facade');
		
		$sellingFrom = new DateTime('now');
		$sellingFrom->modify('+1 day');

		$vat = new Vat(new VatData('vat', 21));
		$em->persist($vat);

		$productData = new ProductData();
		$productData->setName('Name');
		$productData->setVat($vat);
		$productData->setSellingFrom($sellingFrom);
		$productData->setHidden(array(1 => false));
		$product = $productEditFacade->create($productData);

		$em->flush();
		$id = $product->getId();
		$em->clear();
		
		$productVisibilityRepository = $this->getContainer()->get('ss6.shop.product.product_visibility_repository');
		/* @var $productVisibilityRepository \SS6\ShopBundle\Model\Product\ProductVisibilityRepository */
		$productVisibilityRepository->refreshProductsVisibility();

		$productAgain = $em->getRepository(Product::class)->find($id);
		/* @var $productAgain \SS6\ShopBundle\Model\Product\Product */
		
		$this->assertFalse($productAgain->isVisibleOnAnyDomain());
	}
	
	public function testIsVisibleOnAnyDomainWhenSellingInPast() {
		$em = $this->getEntityManager();
		$productEditFacade = $this->getContainer()->get('ss6.shop.product.product_edit_facade');
		
		$sellingTo = new DateTime('now');
		$sellingTo->modify('-1 day');

		$vat = new Vat(new VatData('vat', 21));
		$em->persist($vat);

		$productData = new ProductData();
		$productData->setName('Name');
		$productData->setVat($vat);
		$productData->setSellingTo($sellingTo);
		$productData->setHidden(array(1 => false));
		$product = $productEditFacade->create($productData);

		$em->flush();
		$id = $product->getId();
		$em->clear();
		
		$productVisibilityRepository = $this->getContainer()->get('ss6.shop.product.product_visibility_repository');
		/* @var $productVisibilityRepository \SS6\ShopBundle\Model\Product\ProductVisibilityRepository */
		$productVisibilityRepository->refreshProductsVisibility();
		
		$productAgain = $em->getRepository(Product::class)->find($id);
		/* @var $productAgain \SS6\ShopBundle\Model\Product\Product */
		
		$this->assertFalse($productAgain->isVisibleOnAnyDomain());
	}
	
	public function testIsVisibleOnAnyDomainWhenSellingNow() {
		$em = $this->getEntityManager();
		$productEditFacade = $this->getContainer()->get('ss6.shop.product.product_edit_facade');
		
		$sellingFrom = new DateTime('now');
		$sellingFrom->modify('-1 day');
		$sellingTo = new DateTime('now');
		$sellingTo->modify('+1 day');

		$vat = new Vat(new VatData('vat', 21));
		$em->persist($vat);

		$productData = new ProductData();
		$productData->setName('Name');
		$productData->setVat($vat);
		$productData->setSellingFrom($sellingFrom);
		$productData->setSellingTo($sellingTo);
		$productData->setPrice(100);
		$productData->setHidden(array(1 => false));
		$product = $productEditFacade->create($productData);

		$em->flush();
		$id = $product->getId();
		$em->clear();
		
		$productVisibilityRepository = $this->getContainer()->get('ss6.shop.product.product_visibility_repository');
		/* @var $productVisibilityRepository \SS6\ShopBundle\Model\Product\ProductVisibilityRepository */
		$productVisibilityRepository->refreshProductsVisibility();
		
		$productAgain = $em->getRepository(Product::class)->find($id);
		/* @var $productAgain \SS6\ShopBundle\Model\Product\Product */
		
		$this->assertTrue($productAgain->isVisibleOnAnyDomain());
	}

	public function testIsNotVisibleWhenZeroOrNullPrice() {
		$em = $this->getEntityManager();
		$productEditFacade = $this->getContainer()->get('ss6.shop.product.product_edit_facade');

		$vat = new Vat(new VatData('vat', 21));
		$em->persist($vat);

		$productData = new ProductData();
		$productData->setName('Name');
		$productData->setVat($vat);
		$productData->setPrice(0);
		$productData->setHidden(array(1 => false));
		$product1 = $productEditFacade->create($productData);

		$productData->setPrice(null);
		$product2 = $productEditFacade->create($productData);

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

		$this->assertFalse($product1Again->isVisibleOnAnyDomain());
		$this->assertFalse($product2Again->isVisibleOnAnyDomain());
	}
}
