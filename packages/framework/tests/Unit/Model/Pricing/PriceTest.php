<?php

namespace Tests\FrameworkBundle\Unit\Model\Pricing;

use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Component\Money\Money;
use Shopsys\FrameworkBundle\Model\Pricing\Price;
use Tests\FrameworkBundle\Test\IsMoneyEqual;

class PriceTest extends TestCase
{
    public function testAdd()
    {
        $price = new Price(Money::fromInteger(2), Money::fromInteger(3));
        $priceToAdd = new Price(Money::fromInteger(10), Money::fromInteger(15));
        $actualAddingResult = $price->add($priceToAdd);

        $this->assertThat($actualAddingResult->getPriceWithoutVat(), new IsMoneyEqual(Money::fromInteger(12)));
        $this->assertThat($actualAddingResult->getPriceWithVat(), new IsMoneyEqual(Money::fromInteger(18)));
        $this->assertThat($actualAddingResult->getVatAmount(), new IsMoneyEqual(Money::fromInteger(6)));
    }

    public function testAddIsImmutable()
    {
        $price = new Price(Money::fromInteger(2), Money::fromInteger(3));
        $priceToAdd = new Price(Money::fromInteger(10), Money::fromInteger(15));
        $price->add($priceToAdd);

        $this->assertThat($price->getPriceWithoutVat(), new IsMoneyEqual(Money::fromInteger(2)));
        $this->assertThat($price->getPriceWithVat(), new IsMoneyEqual(Money::fromInteger(3)));
        $this->assertThat($price->getVatAmount(), new IsMoneyEqual(Money::fromInteger(1)));
    }

    public function testSubtract()
    {
        $price = new Price(Money::fromInteger(2), Money::fromInteger(3));
        $priceToSubtract = new Price(Money::fromInteger(10), Money::fromInteger(15));
        $actualAddingResult = $price->subtract($priceToSubtract);

        $this->assertThat($actualAddingResult->getPriceWithoutVat(), new IsMoneyEqual(Money::fromInteger(-8)));
        $this->assertThat($actualAddingResult->getPriceWithVat(), new IsMoneyEqual(Money::fromInteger(-12)));
        $this->assertThat($actualAddingResult->getVatAmount(), new IsMoneyEqual(Money::fromInteger(-4)));
    }

    public function testSubtractIsImmutable()
    {
        $price = new Price(Money::fromInteger(2), Money::fromInteger(3));
        $priceToSubtract = new Price(Money::fromInteger(10), Money::fromInteger(15));
        $price->subtract($priceToSubtract);

        $this->assertThat($price->getPriceWithoutVat(), new IsMoneyEqual(Money::fromInteger(2)));
        $this->assertThat($price->getPriceWithVat(), new IsMoneyEqual(Money::fromInteger(3)));
        $this->assertThat($price->getVatAmount(), new IsMoneyEqual(Money::fromInteger(1)));
    }
}
