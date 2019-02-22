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
        $price = new Price(Money::create(2), Money::create(3));
        $priceToAdd = new Price(Money::create(10), Money::create(15));
        $actualAddingResult = $price->add($priceToAdd);

        $this->assertThat($actualAddingResult->getPriceWithoutVat(), new IsMoneyEqual(Money::create(12)));
        $this->assertThat($actualAddingResult->getPriceWithVat(), new IsMoneyEqual(Money::create(18)));
        $this->assertThat($actualAddingResult->getVatAmount(), new IsMoneyEqual(Money::create(6)));
    }

    public function testAddIsImmutable()
    {
        $price = new Price(Money::create(2), Money::create(3));
        $priceToAdd = new Price(Money::create(10), Money::create(15));
        $price->add($priceToAdd);

        $this->assertThat($price->getPriceWithoutVat(), new IsMoneyEqual(Money::create(2)));
        $this->assertThat($price->getPriceWithVat(), new IsMoneyEqual(Money::create(3)));
        $this->assertThat($price->getVatAmount(), new IsMoneyEqual(Money::create(1)));
    }

    public function testSubtract()
    {
        $price = new Price(Money::create(2), Money::create(3));
        $priceToSubtract = new Price(Money::create(10), Money::create(15));
        $actualAddingResult = $price->subtract($priceToSubtract);

        $this->assertThat($actualAddingResult->getPriceWithoutVat(), new IsMoneyEqual(Money::create(-8)));
        $this->assertThat($actualAddingResult->getPriceWithVat(), new IsMoneyEqual(Money::create(-12)));
        $this->assertThat($actualAddingResult->getVatAmount(), new IsMoneyEqual(Money::create(-4)));
    }

    public function testSubtractIsImmutable()
    {
        $price = new Price(Money::create(2), Money::create(3));
        $priceToSubtract = new Price(Money::create(10), Money::create(15));
        $price->subtract($priceToSubtract);

        $this->assertThat($price->getPriceWithoutVat(), new IsMoneyEqual(Money::create(2)));
        $this->assertThat($price->getPriceWithVat(), new IsMoneyEqual(Money::create(3)));
        $this->assertThat($price->getVatAmount(), new IsMoneyEqual(Money::create(1)));
    }

    public function testInverse()
    {
        $price = new Price(Money::create(2), Money::create(3));
        $actualInverseResult = $price->inverse();

        $this->assertThat($actualInverseResult->getPriceWithoutVat(), new IsMoneyEqual(Money::create(-2)));
        $this->assertThat($actualInverseResult->getPriceWithVat(), new IsMoneyEqual(Money::create(-3)));
        $this->assertThat($actualInverseResult->getVatAmount(), new IsMoneyEqual(Money::create(-1)));
    }

    public function testInverseIsImmutable()
    {
        $price = new Price(Money::create(2), Money::create(3));
        $price->inverse();

        $this->assertThat($price->getPriceWithoutVat(), new IsMoneyEqual(Money::create(2)));
        $this->assertThat($price->getPriceWithVat(), new IsMoneyEqual(Money::create(3)));
        $this->assertThat($price->getVatAmount(), new IsMoneyEqual(Money::create(1)));
    }
}
