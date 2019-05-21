<?php

declare(strict_types=1);

namespace Tests\ReadModelBundle\Unit\Product\Action;

use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Model\Product\Product;
use Shopsys\ReadModelBundle\Product\Action\ProductActionView;
use Shopsys\ReadModelBundle\Product\Action\ProductActionViewFactory;

class ProductActionViewFactoryTest extends TestCase
{
    public function testCreateFromImage(): void
    {
        $productMock = $this->createMock(Product::class);

        $productMock->method('getId')->willReturn(1);
        $productMock->method('isSellingDenied')->willReturn(false);
        $productMock->method('isMainVariant')->willReturn(false);

        $productActionViewFactory = new ProductActionViewFactory();

        $detailUrl = 'http://webserver:8080/product/1';

        $productActionView = $productActionViewFactory->createFromProduct($productMock, $detailUrl);

        $this->assertEquals(new ProductActionView(1, false, false, $detailUrl), $productActionView);
    }
}
