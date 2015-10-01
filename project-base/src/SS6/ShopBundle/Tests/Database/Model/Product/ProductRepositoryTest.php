<?php

namespace SS6\ShopBundle\Tests\Database\Model\Product;

use SS6\ShopBundle\Component\Domain\Domain;
use SS6\ShopBundle\DataFixtures\Base\PricingGroupDataFixture;
use SS6\ShopBundle\DataFixtures\Demo\ProductDataFixture;
use SS6\ShopBundle\Model\Product\ProductRepository;
use SS6\ShopBundle\Tests\Test\DatabaseTestCase;

class ProductRepositoryTest extends DatabaseTestCase{

	public function getAllListableQueryBuilderProvider() {
		return [
			[1, true, 'Visible and not selling denied product is not listed'],
			[6, false, 'Visible and selling denied product is listed'],
			[53, false, 'Product variant is listed'],
			[69, true, 'Product main variant is not listed'],
		];
	}

	/**
	 * @dataProvider getAllListableQueryBuilderProvider
	 */
	public function testGetAllListableQueryBuilder($productReferenceId, $isExpectedInResult, $failMessage) {
		$productRepository = $this->getContainer()->get(ProductRepository::class);
		/* @var $productRepository \SS6\ShopBundle\Model\Product\ProductRepository */
		$pricingGroup = $this->getReference(PricingGroupDataFixture::ORDINARY_DOMAIN_1);
		/* @var $pricingGroup \SS6\ShopBundle\Model\Pricing\Group\PricingGroup */

		$domain = $this->getContainer()->get(Domain::class);
		/* @var $domain \SS6\ShopBundle\Component\Domain\Domain */

		$product = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . $productReferenceId);
		$productId = $product->getId();

		$queryBuilder = $productRepository->getAllListableQueryBuilder($domain->getId(), $pricingGroup->getId());
		$queryBuilder->andWhere('p.id = :id')
			->setParameter('id', $productId);
		$result = $queryBuilder->getQuery()->execute();

		if ($isExpectedInResult) {
			$this->assertContains($product, $result, $failMessage);
		} else {
			$this->assertNotContains($product, $result, $failMessage);
		}
	}

	public function getAllSellableQueryBuilderProvider() {
		return [
			[1, true, 'Visible and not selling denied product is not sellable'],
			[6, false, 'Visible and selling denied product is sellable'],
			[53, true, 'Product variant is not listed'],
			[69, false, 'Product main variant is listed'],
		];
	}

	/**
	 * @dataProvider getAllSellableQueryBuilderProvider
	 */
	public function testGetAllSellableQueryBuilder($productReferenceId, $isExpectedInResult, $failMessage) {
		$productRepository = $this->getContainer()->get(ProductRepository::class);
		/* @var $productRepository \SS6\ShopBundle\Model\Product\ProductRepository */
		$pricingGroup = $this->getReference(PricingGroupDataFixture::ORDINARY_DOMAIN_1);
		/* @var $pricingGroup \SS6\ShopBundle\Model\Pricing\Group\PricingGroup */

		$domain = $this->getContainer()->get(Domain::class);
		/* @var $domain \SS6\ShopBundle\Component\Domain\Domain */

		$product = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . $productReferenceId);
		$productId = $product->getId();

		$queryBuilder = $productRepository->getAllSellableQueryBuilder($domain->getId(), $pricingGroup->getId());
		$queryBuilder->andWhere('p.id = :id')
			->setParameter('id', $productId);
		$result = $queryBuilder->getQuery()->execute();

		if ($isExpectedInResult) {
			$this->assertContains($product, $result, $failMessage);
		} else {
			$this->assertNotContains($product, $result, $failMessage);
		}
	}

}
