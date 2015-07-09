<?php

namespace SS6\ShopBundle\Tests\Database\Model\Product;

use SS6\ShopBundle\Model\Domain\Domain;
use SS6\ShopBundle\Model\Product\ProductRepository;
use SS6\ShopBundle\Tests\Test\DatabaseTestCase;

class ProductRepositoryTest extends DatabaseTestCase{

	public function testGetAllListableQueryBuilder() {
		$productRepository = $this->getContainer()->get(ProductRepository::class);
		/* @var $productRepository \SS6\ShopBundle\Model\Product\ProductRepository */
		$pricingGroup = $this->getReference('pricing_group_ordinary_domain_1');
		/* @var $pricingGroup \SS6\ShopBundle\Model\Pricing\Group\PricingGroup */

		$domain = $this->getContainer()->get(Domain::class);
		/* @var $domain \SS6\ShopBundle\Model\Domain\Domain */

		$product = $this->getReference('product_6');
		$productId = $product->getId();

		$queryBuilder = $productRepository->getAllListableQueryBuilder($domain->getId(), $pricingGroup->getId());
		$queryBuilder->andWhere('p.id = :id')
			->setParameter('id', $productId);
		$result = $queryBuilder->getQuery()->execute();

		$this->assertEmpty($result, 'Product with denied selling was listed');
	}
}
