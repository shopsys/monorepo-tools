<?php

declare(strict_types=1);

namespace Tests\ReadModelBundle\Unit\Product\Action;

use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Model\Product\Collection\ProductCollectionFacade;
use Shopsys\FrameworkBundle\Model\Product\Product;
use Shopsys\ReadModelBundle\Product\Action\ProductActionView;
use Shopsys\ReadModelBundle\Product\Action\ProductActionViewFacade;
use Shopsys\ReadModelBundle\Product\Action\ProductActionViewFactory;

class ProductActionViewFacadeTest extends TestCase
{
    public function testGetForProducts(): void
    {
        $productActionViewFactory = new ProductActionViewFactory();

        $domain = $this->createDomainMock();

        $productCollectionFacade = $this->createMock(ProductCollectionFacade::class);
        $productCollectionFacade->method('getAbsoluteUrlsIndexedByProductId')->willReturn([
            1 => 'http://http://webserver:8080/product/1',
            2 => 'http://http://webserver:8080/product/2',
            3 => 'http://http://webserver:8080/product/3',
        ]);

        $productActionViewFacade = new ProductActionViewFacade($productCollectionFacade, $domain, $productActionViewFactory);

        $productActionViews = $productActionViewFacade->getForProducts([
            $this->createProductMock(1),
            $this->createProductMock(2),
            $this->createProductMock(3),
        ]);

        $expected = [
            1 => new ProductActionView(1, false, false, 'http://http://webserver:8080/product/1'),
            2 => new ProductActionView(2, false, false, 'http://http://webserver:8080/product/2'),
            3 => new ProductActionView(3, false, false, 'http://http://webserver:8080/product/3'),
        ];

        $this->assertEquals($expected, $productActionViews);
    }

    /**
     * @param int $id
     * @return \Shopsys\FrameworkBundle\Model\Product\Product
     */
    private function createProductMock(int $id): Product
    {
        $productMock = $this->createMock(Product::class);

        $productMock->method('getId')->willReturn($id);
        $productMock->method('isSellingDenied')->willReturn(false);
        $productMock->method('isMainVariant')->willReturn(false);

        return $productMock;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    protected function createDomainMock(): Domain
    {
        $domainConfig = new DomainConfig(1, 'http://webserver:8080/', 'shopsys', 'en');

        $domain = $this->createMock(Domain::class);
        $domain->method('getCurrentDomainConfig')->willReturn($domainConfig);

        return $domain;
    }
}
