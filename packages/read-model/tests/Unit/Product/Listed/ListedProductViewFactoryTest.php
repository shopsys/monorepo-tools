<?php

namespace Tests\ReadModelBundle\Unit\Product\Listed;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\Money\Money;
use Shopsys\FrameworkBundle\Model\Pricing\Price;
use Shopsys\FrameworkBundle\Model\Product\Availability\Availability;
use Shopsys\FrameworkBundle\Model\Product\Flag\Flag;
use Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPrice;
use Shopsys\FrameworkBundle\Model\Product\Product;
use Shopsys\FrameworkBundle\Model\Product\ProductCachedAttributesFacade;
use Shopsys\ReadModelBundle\Image\ImageView;
use Shopsys\ReadModelBundle\Product\Action\ProductActionView;
use Shopsys\ReadModelBundle\Product\Listed\ListedProductView;
use Shopsys\ReadModelBundle\Product\Listed\ListedProductViewFactory;

class ListedProductViewFactoryTest extends TestCase
{
    /**
     * @dataProvider getProductsData
     * @param int $id
     * @param string $productName
     * @param string $shortDescription
     * @param string $availabilityName
     * @param int $priceAmount
     * @param \Shopsys\ReadModelBundle\Image\ImageView $imageView
     * @param \Shopsys\ReadModelBundle\Product\Action\ProductActionView $productActionView
     * @param \Shopsys\FrameworkBundle\Model\Product\Flag\Flag[]|\Doctrine\Common\Collections\Collection $flags
     * @param int[] $expectedFlags
     */
    public function testCreateFromProduct(
        int $id,
        string $productName,
        string $shortDescription,
        string $availabilityName,
        int $priceAmount,
        ImageView $imageView,
        ProductActionView $productActionView,
        $flags,
        $expectedFlags
    ): void {
        $domainMock = $this->createDomainMock();
        $productCachedAttributesFacadeMock = $this->createProductCachedAttributesFacadeMock($priceAmount);

        $listedProductViewFactory = new ListedProductViewFactory($domainMock, $productCachedAttributesFacadeMock);

        $productMock = $this->createProductMock($id, $productName, $shortDescription, $availabilityName, $flags);

        $listedProductView = $listedProductViewFactory->createFromProduct($productMock, $imageView, $productActionView);

        $expected = new ListedProductView(
            $id,
            $productName,
            $shortDescription,
            $availabilityName,
            $this->createProductPrice($priceAmount),
            $expectedFlags,
            $productActionView,
            $imageView
        );

        $this->assertEquals($expected, $listedProductView);
    }

    /**
     * @param int $id
     * @param string $name
     * @param string $shortDescription
     * @param string $availabilityName
     * @param \Shopsys\FrameworkBundle\Model\Product\Flag\Flag[]|\Doctrine\Common\Collections\Collection $flags
     * @return \PHPUnit\Framework\MockObject\MockObject|\Shopsys\FrameworkBundle\Model\Product\Product
     */
    private function createProductMock(int $id, string $name, string $shortDescription, string $availabilityName, $flags)
    {
        $productMock = $this->createMock(Product::class);

        $productMock->method('getId')->willReturn($id);
        $productMock->method('getName')->willReturn($name);
        $productMock->method('getShortDescription')->willReturn($shortDescription);
        $productMock->method('getFlags')->willReturn($flags);

        $productAvailabilityMock = $this->createMock(Availability::class);
        $productAvailabilityMock->method('getName')->willReturn($availabilityName);

        $productMock->method('getCalculatedAvailability')->willReturn($productAvailabilityMock);

        return $productMock;
    }

    /**
     * @param int $id
     * @return \PHPUnit\Framework\MockObject\MockObject|\Shopsys\FrameworkBundle\Model\Product\Flag\Flag
     */
    private function createFlagMock(int $id)
    {
        $flagMock = $this->createMock(Flag::class);
        $flagMock->method('getId')->willReturn($id);

        return $flagMock;
    }

    /**
     * @return array
     */
    public function getProductsData(): array
    {
        return [
            [
                'id' => 1,
                'productName' => '22" Sencor SLE 22F46DM4 HELLO KITTY',
                'shortDescription' => 'short description',
                'availabilityName' => 'available',
                'priceAmount' => 100,
                'imageView' => new ImageView(1, 'jpg', 'product', null),
                'productActionView' => new ProductActionView(1, false, false, 'http://webserver:8080/product/1'),
                'flags' => [
                    $this->createFlagMock(1),
                    $this->createFlagMock(5),
                ],
                'expectedFlagIds' => [1, 5],
            ],
            [
                'id' => 2,
                'productName' => '32" Philips 32PFL4308',
                'short description' => 'even shorter description',
                'availabilityName' => 'sold out',
                'priceAmount' => 45,
                'imageView' => new ImageView(2, 'jpg', 'product', null),
                'productActionView' => new ProductActionView(2, false, false, 'http://webserver:8080/product/2'),
                'flags' => new ArrayCollection([
                    $this->createFlagMock(4),
                    $this->createFlagMock(3),
                ]),
                'expectedFlagIds' => [4, 3],
            ],
        ];
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    private function createDomainMock()
    {
        $domainMock = $this->createMock(Domain::class);
        $domainMock->method('getId')->willReturn(1);
        return $domainMock;
    }

    /**
     * @param int $priceAmount
     * @return \PHPUnit\Framework\MockObject\MockObject|\Shopsys\FrameworkBundle\Model\Product\ProductCachedAttributesFacade
     */
    private function createProductCachedAttributesFacadeMock(int $priceAmount)
    {
        $productCachedAttributesFacadeMock = $this->createMock(ProductCachedAttributesFacade::class);
        $productCachedAttributesFacadeMock->method('getProductSellingPrice')->willReturn(
            $this->createProductPrice($priceAmount)
        );
        return $productCachedAttributesFacadeMock;
    }

    /**
     * @param int $amount
     * @return \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPrice
     */
    private function createProductPrice(int $amount): ProductPrice
    {
        return new ProductPrice(new Price(Money::create($amount), Money::create($amount)), false);
    }
}
