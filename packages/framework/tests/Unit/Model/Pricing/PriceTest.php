<?php

namespace Tests\FrameworkBundle\Unit\Model\Pricing;

use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Component\Money\Money;
use Shopsys\FrameworkBundle\Model\Pricing\Price;

class PriceTest extends TestCase
{
    public function testAdd()
    {
        $price = new Price(Money::fromInteger(2), Money::fromInteger(3));
        $priceToAdd = new Price(Money::fromInteger(10), Money::fromInteger(15));
        $actualAddingResult = $price->add($priceToAdd);

        $this->assertSame(12, $actualAddingResult->getPriceWithoutVat());
        $this->assertSame(18, $actualAddingResult->getPriceWithVat());
        $this->assertSame(6, $actualAddingResult->getVatAmount());
    }

    public function testAddIsImmutable()
    {
        $price = new Price(Money::fromInteger(2), Money::fromInteger(3));
        $priceToAdd = new Price(Money::fromInteger(10), Money::fromInteger(15));
        $price->add($priceToAdd);

        $this->assertSame(2, $price->getPriceWithoutVat());
        $this->assertSame(3, $price->getPriceWithVat());
        $this->assertSame(1, $price->getVatAmount());
    }

    public function testSubtract()
    {
        $price = new Price(Money::fromInteger(2), Money::fromInteger(3));
        $priceToSubtract = new Price(Money::fromInteger(10), Money::fromInteger(15));
        $actualAddingResult = $price->subtract($priceToSubtract);

        $this->assertSame(-8, $actualAddingResult->getPriceWithoutVat());
        $this->assertSame(-12, $actualAddingResult->getPriceWithVat());
        $this->assertSame(-4, $actualAddingResult->getVatAmount());
    }

    public function testSubtractIsImmutable()
    {
        $price = new Price(Money::fromInteger(2), Money::fromInteger(3));
        $priceToSubtract = new Price(Money::fromInteger(10), Money::fromInteger(15));
        $price->subtract($priceToSubtract);

        $this->assertSame(2, $price->getPriceWithoutVat());
        $this->assertSame(3, $price->getPriceWithVat());
        $this->assertSame(1, $price->getVatAmount());
    }
}
