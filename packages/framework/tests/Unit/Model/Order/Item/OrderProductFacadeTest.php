<?php

declare(strict_types=1);

namespace Tests\FrameworkBundle\Unit\Model\Order\Item;

use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver;
use Shopsys\FrameworkBundle\Model\Module\ModuleFacade;
use Shopsys\FrameworkBundle\Model\Order\Item\OrderItem;
use Shopsys\FrameworkBundle\Model\Order\Item\OrderProductFacade;
use Shopsys\FrameworkBundle\Model\Order\Order;
use Shopsys\FrameworkBundle\Model\Pricing\Price;
use Shopsys\FrameworkBundle\Model\Product\Availability\ProductAvailabilityRecalculationScheduler;
use Shopsys\FrameworkBundle\Model\Product\Product;
use Shopsys\FrameworkBundle\Model\Product\ProductCategoryDomainFactory;
use Shopsys\FrameworkBundle\Model\Product\ProductData;
use Shopsys\FrameworkBundle\Model\Product\ProductHiddenRecalculator;
use Shopsys\FrameworkBundle\Model\Product\ProductSellingDeniedRecalculator;
use Shopsys\FrameworkBundle\Model\Product\ProductVisibilityFacade;

final class OrderProductFacadeTest extends TestCase
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\Item\OrderProductFacade
     */
    private $orderProductFacade;

    protected function setUp(): void
    {
        parent::setUp();

        $moduleFacadeMock = $this->getMockBuilder(ModuleFacade::class)
            ->setMethods(['isEnabled'])
            ->disableOriginalConstructor()
            ->getMock();
        $moduleFacadeMock->expects($this->any())->method('isEnabled')->willReturn(true);

        $this->orderProductFacade = new OrderProductFacade(
            $this->createMock(EntityManager::class),
            $this->createMock(ProductHiddenRecalculator::class),
            $this->createMock(ProductSellingDeniedRecalculator::class),
            $this->createMock(ProductAvailabilityRecalculationScheduler::class),
            $this->createMock(ProductVisibilityFacade::class),
            $moduleFacadeMock
        );
    }

    /**
     * @return iterable
     */
    public function subtractOrderProductsFromStockUsingStockProvider(): iterable
    {
        yield [15, 10, 5];
        yield [10, 10, 0];
        yield [5, 0, 5];
        yield [0, 5, -5];
    }

    /**
     * @dataProvider subtractOrderProductsFromStockUsingStockProvider
     * @param int $stockQuantity
     * @param int $orderedQuantity
     * @param int $expectedStockQuantity
     */
    public function testSubtractOrderProductsFromStockUsingStock(int $stockQuantity, int $orderedQuantity, int $expectedStockQuantity): void
    {
        $product = $this->createProductWithStockQuantity($stockQuantity);
        $orderProduct = $this->createOrderItem($product, $orderedQuantity);

        $this->orderProductFacade->subtractOrderProductsFromStock([$orderProduct]);

        $this->assertSame($expectedStockQuantity, $product->getStockQuantity());
    }

    /**
     * @return iterable
     */
    public function addOrderProductsFromStockUsingStockProvider(): iterable
    {
        yield [15, 10, 25];
        yield [10, 10, 20];
        yield [5, 0, 5];
        yield [0, 5, 5];
    }

    /**
     * @dataProvider addOrderProductsFromStockUsingStockProvider
     * @param int $stockQuantity
     * @param int $orderedQuantity
     * @param int $expectedStockQuantity
     */
    public function testAddOrderProductsToStockUsingStock(int $stockQuantity, int $orderedQuantity, int $expectedStockQuantity): void
    {
        $product = $this->createProductWithStockQuantity($stockQuantity);
        $orderProduct = $this->createOrderItem($product, $orderedQuantity);

        $this->orderProductFacade->addOrderProductsToStock([$orderProduct]);

        $this->assertSame($expectedStockQuantity, $product->getStockQuantity());
    }

    /**
     * @param int $productStockQuantity
     * @return \Shopsys\FrameworkBundle\Model\Product\Product
     */
    private function createProductWithStockQuantity(int $productStockQuantity): Product
    {
        $productData = new ProductData();
        $productData->usingStock = true;
        $productData->stockQuantity = $productStockQuantity;
        $productData->outOfStockAction = Product::OUT_OF_STOCK_ACTION_HIDE;

        return Product::create($productData, new ProductCategoryDomainFactory(new EntityNameResolver([])));
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param int $orderProductQuantity
     * @return \Shopsys\FrameworkBundle\Model\Order\Item\OrderItem
     */
    private function createOrderItem(Product $product, int $orderProductQuantity): OrderItem
    {
        $orderProduct = new OrderItem(
            $this->createMock(Order::class),
            'productName',
            Price::zero(),
            '0',
            $orderProductQuantity,
            OrderItem::TYPE_PRODUCT,
            null,
            null
        );
        $orderProduct->setProduct($product);

        return $orderProduct;
    }
}
