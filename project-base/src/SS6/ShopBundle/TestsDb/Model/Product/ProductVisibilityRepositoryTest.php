<?php

namespace SS6\ShopBundle\TestsDb\Model\Product;

use DateTime;
use SS6\ShopBundle\Component\Test\DatabaseTestCase;
use SS6\ShopBundle\Model\Pricing\Vat\Vat;
use SS6\ShopBundle\Model\Pricing\Vat\VatData;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\ProductData;
use SS6\ShopBundle\Model\Product\ProductDomain;

class ProductVisibilityRepositoryTest extends DatabaseTestCase {

	private function getDefaultProductData() {
		$em = $this->getEntityManager();
		$vat = new Vat(new VatData('vat', 21));
		$em->persist($vat);

		$productData = new ProductData();
		$productData->setName(['cs' => 'Name']);
		$productData->setVat($vat);
		$productData->setPrice(100);
		$productData->setHidden(false);
		$productData->setHiddenOnDomains(array());
		return $productData;
	}

	public function testIsVisibleOnAnyDomainWhenHidden() {
		$em = $this->getEntityManager();
		$productEditFacade = $this->getContainer()->get('ss6.shop.product.product_edit_facade');

		$productData = $this->getDefaultProductData();
		$productData->setHidden(true);
		$product = $productEditFacade->create($productData);

		$em->flush();
		$id = $product->getId();
		$em->clear();

		$productVisibilityRepository = $this->getContainer()->get('ss6.shop.product.product_visibility_repository');
		/* @var $productVisibilityRepository \SS6\ShopBundle\Model\Product\ProductVisibilityRepository */
		$productVisibilityRepository->refreshProductsVisibility();

		$productAgain = $em->getRepository(Product::class)->find($id);
		/* @var $productAgain \SS6\ShopBundle\Model\Product\Product */

		$productDomain1 = $em->getRepository(ProductDomain::class)->findOneBy(array(
			'product' => $productAgain,
			'domainId' => 1,
		));
		/* @var $productDomain1 \SS6\ShopBundle\Model\Product\ProductDomain */

		$this->assertFalse($productAgain->isVisible());
		$this->assertFalse($productDomain1->isVisible());
	}

	public function testIsVisibleOnAnyDomainWhenNotHidden() {
		$em = $this->getEntityManager();
		$productEditFacade = $this->getContainer()->get('ss6.shop.product.product_edit_facade');

		$productData = $this->getDefaultProductData();
		$product = $productEditFacade->create($productData);

		$em->flush();
		$id = $product->getId();
		$em->clear();

		$productVisibilityRepository = $this->getContainer()->get('ss6.shop.product.product_visibility_repository');
		/* @var $productVisibilityRepository \SS6\ShopBundle\Model\Product\ProductVisibilityRepository */
		$productVisibilityRepository->refreshProductsVisibility();

		$productAgain = $em->getRepository(Product::class)->find($id);
		/* @var $productAgain \SS6\ShopBundle\Model\Product\Product */

		$productDomain1 = $em->getRepository(ProductDomain::class)->findOneBy(array(
			'product' => $productAgain,
			'domainId' => 1,
		));
		/* @var $productDomain1 \SS6\ShopBundle\Model\Product\ProductDomain */

		$this->assertTrue($productAgain->isVisible());
		$this->assertTrue($productDomain1->isVisible());
	}

	public function testIsVisibleOnAnyDomainWhenSellingInFuture() {
		$em = $this->getEntityManager();
		$productEditFacade = $this->getContainer()->get('ss6.shop.product.product_edit_facade');

		$sellingFrom = new DateTime('now');
		$sellingFrom->modify('+1 day');

		$productData = $this->getDefaultProductData();
		$productData->setSellingFrom($sellingFrom);
		$product = $productEditFacade->create($productData);

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

	public function testIsVisibleOnAnyDomainWhenSellingInPast() {
		$em = $this->getEntityManager();
		$productEditFacade = $this->getContainer()->get('ss6.shop.product.product_edit_facade');

		$sellingTo = new DateTime('now');
		$sellingTo->modify('-1 day');

		$productData = $this->getDefaultProductData();
		$productData->setSellingTo($sellingTo);
		$product = $productEditFacade->create($productData);

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

	public function testIsVisibleOnAnyDomainWhenSellingNow() {
		$em = $this->getEntityManager();
		$productEditFacade = $this->getContainer()->get('ss6.shop.product.product_edit_facade');

		$sellingFrom = new DateTime('now');
		$sellingFrom->modify('-1 day');
		$sellingTo = new DateTime('now');
		$sellingTo->modify('+1 day');

		$productData = $this->getDefaultProductData();
		$productData->setSellingFrom($sellingFrom);
		$productData->setSellingTo($sellingTo);
		$product = $productEditFacade->create($productData);

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
		$productEditFacade = $this->getContainer()->get('ss6.shop.product.product_edit_facade');

		$productData = $this->getDefaultProductData();
		$productData->setPrice(0);
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

		$this->assertFalse($product1Again->isVisible());
		$this->assertFalse($product2Again->isVisible());
	}

	public function testIsVisibleWithEmptyName() {
		$em = $this->getEntityManager();
		$productEditFacade = $this->getContainer()->get('ss6.shop.product.product_edit_facade');
		/* @var $productEditFacade \SS6\ShopBundle\Model\Product\ProductEditFacade */

		$productData = $this->getDefaultProductData();
		$productData->setName(['cs' => 'Name']);
		$product = $productEditFacade->create($productData);

		$productId = $product->getId();
		$em->clear();

		$productVisibilityRepository = $this->getContainer()->get('ss6.shop.product.product_visibility_repository');
		/* @var $productVisibilityRepository \SS6\ShopBundle\Model\Product\ProductVisibilityRepository */
		$productVisibilityRepository->refreshProductsVisibility();

		$productFromDb = $em->getRepository(Product::class)->find($productId);
		/* @var $productFromDb \SS6\ShopBundle\Model\Product\Product */

		$productDomain1 = $em->getRepository(ProductDomain::class)->findOneBy(array(
			'product' => $productId,
			'domainId' => 1,
		));
		/* @var $productDomain1 \SS6\ShopBundle\Model\Product\ProductDomain */

		$productDomain2 = $em->getRepository(ProductDomain::class)->findOneBy(array(
			'product' => $productId,
			'domainId' => 2,
		));
		/* @var $productDomain2 \SS6\ShopBundle\Model\Product\ProductDomain */

		$this->assertTrue($productFromDb->isVisible());
		$this->assertTrue($productDomain1->isVisible());
		$this->assertFalse($productDomain2->isVisible());
	}

}
