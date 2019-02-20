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

        $this->assertTrue($actualAddingResult->getPriceWithoutVat()->equals(Money::fromInteger(12)));
        $this->assertTrue($actualAddingResult->getPriceWithVat()->equals(Money::fromInteger(18)));
        $this->assertTrue($actualAddingResult->getVatAmount()->equals(Money::fromInteger(6)));
    }

    public function testAddIsImmutable()
    {
        $price = new Price(Money::fromInteger(2), Money::fromInteger(3));
        $priceToAdd = new Price(Money::fromInteger(10), Money::fromInteger(15));
        $price->add($priceToAdd);

        $this->assertTrue($price->getPriceWithoutVat()->equals(Money::fromInteger(2)));
        $this->assertTrue($price->getPriceWithVat()->equals(Money::fromInteger(3)));
        $this->assertTrue($price->getVatAmount()->equals(Money::fromInteger(1)));
    }

    public function testSubtract()
    {
        $price = new Price(Money::fromInteger(2), Money::fromInteger(3));
        $priceToSubtract = new Price(Money::fromInteger(10), Money::fromInteger(15));
        $actualAddingResult = $price->subtract($priceToSubtract);

        $this->assertTrue($actualAddingResult->getPriceWithoutVat()->equals(Money::fromInteger(-8)));
        $this->assertTrue($actualAddingResult->getPriceWithVat()->equals(Money::fromInteger(-12)));
        $this->assertTrue($actualAddingResult->getVatAmount()->equals(Money::fromInteger(-4)));
    }

    public function testSubtractIsImmutable()
    {
        $price = new Price(Money::fromInteger(2), Money::fromInteger(3));
        $priceToSubtract = new Price(Money::fromInteger(10), Money::fromInteger(15));
        $price->subtract($priceToSubtract);

        $this->assertTrue($price->getPriceWithoutVat()->equals(Money::fromInteger(2)));
        $this->assertTrue($price->getPriceWithVat()->equals(Money::fromInteger(3)));
        $this->assertTrue($price->getVatAmount()->equals(Money::fromInteger(1)));
    }
}
