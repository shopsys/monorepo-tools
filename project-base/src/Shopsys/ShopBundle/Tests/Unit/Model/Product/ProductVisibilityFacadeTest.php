<?php

namespace Shopsys\ShopBundle\Tests\Unit\Model\Product;

use PHPUnit_Framework_TestCase;
use Shopsys\ShopBundle\Model\Product\ProductVisibilityFacade;
use Shopsys\ShopBundle\Model\Product\ProductVisibilityRepository;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class ProductVisibilityFacadeTest extends PHPUnit_Framework_TestCase
{
    public function testOnKernelResponseRecalc() {
        $productVisibilityRepositoryMock = $this->getMock(ProductVisibilityRepository::class, [], [], '', false);
        $productVisibilityRepositoryMock
            ->expects($this->once())
            ->method('refreshProductsVisibility')
            ->with($this->equalTo(true));

        $productVisibilityFacade = new ProductVisibilityFacade($productVisibilityRepositoryMock);
        $productVisibilityFacade->refreshProductsVisibilityForMarkedDelayed();

        $eventMock = $this->getMockBuilder(FilterResponseEvent::class)
            ->disableOriginalConstructor()
            ->setMethods(['isMasterRequest'])
            ->getMock();
        $eventMock->expects($this->any())->method('isMasterRequest')
            ->willReturn(true);

        $productVisibilityFacade->onKernelResponse($eventMock);
    }

    public function testOnKernelResponseNoRecalc() {
        $productVisibilityRepositoryMock = $this->getMock(ProductVisibilityRepository::class, [], [], '', false);
        $productVisibilityRepositoryMock->expects($this->never())->method('refreshProductsVisibility');

        $productVisibilityFacade = new ProductVisibilityFacade($productVisibilityRepositoryMock);

        $eventMock = $this->getMockBuilder(FilterResponseEvent::class)
            ->disableOriginalConstructor()
            ->setMethods(['isMasterRequest'])
            ->getMock();
        $eventMock->expects($this->any())->method('isMasterRequest')
            ->willReturn(true);

        $productVisibilityFacade->onKernelResponse($eventMock);
    }

    public function testRefreshProductsVisibility() {
        $productVisibilityRepositoryMock = $this->getMock(ProductVisibilityRepository::class, [], [], '', false);
        $productVisibilityRepositoryMock->expects($this->once())->method('refreshProductsVisibility');

        $productVisibilityFacade = new ProductVisibilityFacade($productVisibilityRepositoryMock);
        $productVisibilityFacade->refreshProductsVisibility();
    }

    public function testRefreshProductsVisibilityForMarked() {
        $productVisibilityRepositoryMock = $this->getMock(ProductVisibilityRepository::class, [], [], '', false);
        $productVisibilityRepositoryMock
            ->expects($this->once())
            ->method('refreshProductsVisibility')
            ->with($this->equalTo(true));

        $productVisibilityFacade = new ProductVisibilityFacade($productVisibilityRepositoryMock);
        $productVisibilityFacade->refreshProductsVisibilityForMarked();
    }
}
