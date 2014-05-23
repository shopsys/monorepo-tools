<?php

namespace SS6\ShopBundle\Tests\Model\Product;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Product\ProductVisibilityFacade;
use SS6\ShopBundle\Model\Product\ProductVisibilityRepository;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class ProductVisibilityFacadeTest extends PHPUnit_Framework_TestCase {

	public function testOnKernelResponseRecalc() {
		$productVisibilityRepositoryMock = $this->getMock(ProductVisibilityRepository::class, array(), array(), '', false);
		$productVisibilityRepositoryMock->expects($this->once())->method('refreshProductsVisibility');

		$productVisibilityFacade = new ProductVisibilityFacade($productVisibilityRepositoryMock);
		$productVisibilityFacade->refreshProductsVisibility();

		$eventMock = $this->getMock(FilterResponseEvent::class, array(), array(), '', false);
		$productVisibilityFacade->onKernelResponse($eventMock);
	}

	public function testOnKernelResponseNoRecalc() {
		$productVisibilityRepositoryMock = $this->getMock(ProductVisibilityRepository::class, array(), array(), '', false);
		$productVisibilityRepositoryMock->expects($this->never())->method('refreshProductsVisibility');

		$productVisibilityFacade = new ProductVisibilityFacade($productVisibilityRepositoryMock);

		$eventMock = $this->getMock(FilterResponseEvent::class, array(), array(), '', false);
		$productVisibilityFacade->onKernelResponse($eventMock);
	}
}
