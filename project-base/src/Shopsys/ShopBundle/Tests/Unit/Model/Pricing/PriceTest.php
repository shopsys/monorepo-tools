<?php

namespace Shopsys\ShopBundle\Tests\Unit\Model\Pricing;

use PHPUnit_Framework_TestCase;
use Shopsys\ShopBundle\Model\Pricing\Price;

class PriceTest extends PHPUnit_Framework_TestCase
{

    public function testAdd() {
        $price = new Price(2, 3);
        $priceToAdd = new Price(10, 15);
        $actualAddingResult = $price->add($priceToAdd);

        $this->assertSame(12, $actualAddingResult->getPriceWithoutVat());
        $this->assertSame(18, $actualAddingResult->getPriceWithVat());
        $this->assertSame(6, $actualAddingResult->getVatAmount());
    }

    public function testAddIsImmutable() {
        $price = new Price(2, 3);
        $priceToAdd = new Price(10, 15);
        $price->add($priceToAdd);

        $this->assertSame(2, $price->getPriceWithoutVat());
        $this->assertSame(3, $price->getPriceWithVat());
        $this->assertSame(1, $price->getVatAmount());
    }

    public function testSubtract() {
        $price = new Price(2, 3);
        $priceToSubtract = new Price(10, 15);
        $actualAddingResult = $price->subtract($priceToSubtract);

        $this->assertSame(-8, $actualAddingResult->getPriceWithoutVat());
        $this->assertSame(-12, $actualAddingResult->getPriceWithVat());
        $this->assertSame(-4, $actualAddingResult->getVatAmount());
    }

    public function testSubtractIsImmutable() {
        $price = new Price(2, 3);
        $priceToSubtract = new Price(10, 15);
        $price->subtract($priceToSubtract);

        $this->assertSame(2, $price->getPriceWithoutVat());
        $this->assertSame(3, $price->getPriceWithVat());
        $this->assertSame(1, $price->getVatAmount());
    }

}
