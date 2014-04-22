<?php

namespace SS6\ShopBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ProductVisibilityData extends AbstractFixture implements ContainerAwareInterface, OrderedFixtureInterface {
	/**
	 * @var \Symfony\Component\DependencyInjection\ContainerInterface
	 */
	private $container;
	
	/**
	 * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
	 */
	public function setContainer(ContainerInterface $container = null) {
		$this->container = $container;
	}
	
	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {
		$productVisibilityRepository = $this->container->get('ss6.shop.product.product_visibility_repository');
		/* @var $productVisibilityRepository \SS6\ShopBundle\Model\Product\ProductVisibilityRepository */
		$productVisibilityRepository->refreshProductsVisibility();
	}

	/**
	 * @return int
	 */
	public function getOrder() {
		return 1000; // must be last fixture (after all that use ORM)
	}	
}
