<?php

namespace Tests\ShopBundle\Unit\Model\Transport;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit_Framework_TestCase;
use Shopsys\ShopBundle\Model\Payment\IndependentPaymentVisibilityCalculation;
use Shopsys\ShopBundle\Model\Payment\Payment;
use Shopsys\ShopBundle\Model\Transport\IndependentTransportVisibilityCalculation;
use Shopsys\ShopBundle\Model\Transport\Transport;
use Shopsys\ShopBundle\Model\Transport\TransportVisibilityCalculation;

class TransportVisibilityCalculationTest extends PHPUnit_Framework_TestCase
{
    public function testIsVisibleWhenIndepentlyInvisible()
    {
        $domainId = 1;
        $transportMock = $this->getMock(Transport::class, [], [], '', false);

        $independentTransportVisibilityCalculationMock = $this
            ->getMock(IndependentTransportVisibilityCalculation::class, ['isIndependentlyVisible'], [], '', false);
        $independentTransportVisibilityCalculationMock
            ->expects($this->atLeastOnce())
            ->method('isIndependentlyVisible')
            ->with($this->equalTo($transportMock), $this->equalTo($domainId))
            ->willReturn(false);

        $independentPaymentVisibilityCalculationMock = $this
            ->getMock(IndependentPaymentVisibilityCalculation::class, [], [], '', false);

        $transportVisibilityCalculation = new TransportVisibilityCalculation(
            $independentTransportVisibilityCalculationMock,
            $independentPaymentVisibilityCalculationMock
        );

        $this->assertFalse($transportVisibilityCalculation->isVisible($transportMock, [], $domainId));
    }

    public function testIsVisibleWithHiddenPayment()
    {
        $domainId = 1;
        $transportMock = $this->getMock(Transport::class, [], [], '', false);
        $paymentMock = $this->getMock(Payment::class, [], [], '', false);

        $independentTransportVisibilityCalculationMock = $this
            ->getMock(IndependentTransportVisibilityCalculation::class, ['isIndependentlyVisible'], [], '', false);
        $independentTransportVisibilityCalculationMock
            ->expects($this->atLeastOnce())
            ->method('isIndependentlyVisible')
            ->with($this->equalTo($transportMock), $this->equalTo($domainId))
            ->willReturn(true);

        $independentPaymentVisibilityCalculationMock = $this
            ->getMock(IndependentPaymentVisibilityCalculation::class, ['isIndependentlyVisible'], [], '', false);
        $independentPaymentVisibilityCalculationMock
            ->expects($this->atLeastOnce())
            ->method('isIndependentlyVisible')
            ->with($this->equalTo($paymentMock), $this->equalTo($domainId))
            ->willReturn(false);

        $transportVisibilityCalculation = new TransportVisibilityCalculation(
            $independentTransportVisibilityCalculationMock,
            $independentPaymentVisibilityCalculationMock
        );

        $this->assertFalse($transportVisibilityCalculation->isVisible($transportMock, [$paymentMock], $domainId));
    }

    public function testIsVisibleWithoutPayment()
    {
        $domainId = 1;
        $transportMock = $this->getMock(Transport::class, [], [], '', false);
        $paymentMock = $this->getMock(Payment::class, ['getTransports'], [], '', false);
        $paymentMock->expects($this->atLeastOnce())->method('getTransports')->willReturn(new ArrayCollection([]));

        $independentTransportVisibilityCalculationMock = $this
            ->getMock(IndependentTransportVisibilityCalculation::class, ['isIndependentlyVisible'], [], '', false);
        $independentTransportVisibilityCalculationMock
            ->expects($this->atLeastOnce())
            ->method('isIndependentlyVisible')
            ->with($this->equalTo($transportMock), $this->equalTo($domainId))
            ->willReturn(true);

        $independentPaymentVisibilityCalculationMock = $this
            ->getMock(IndependentPaymentVisibilityCalculation::class, ['isIndependentlyVisible'], [], '', false);
        $independentPaymentVisibilityCalculationMock
            ->expects($this->atLeastOnce())
            ->method('isIndependentlyVisible')
            ->with($this->equalTo($paymentMock), $this->equalTo($domainId))
            ->willReturn(true);

        $transportVisibilityCalculation = new TransportVisibilityCalculation(
            $independentTransportVisibilityCalculationMock,
            $independentPaymentVisibilityCalculationMock
        );

        $this->assertFalse($transportVisibilityCalculation->isVisible($transportMock, [$paymentMock], $domainId));
    }

    public function testIsVisibleWithVisiblePayment()
    {
        $domainId = 1;
        $transportMock = $this->getMock(Transport::class, [], [], '', false);
        $paymentMock = $this->getMock(Payment::class, ['getTransports'], [], '', false);
        $paymentMock->expects($this->atLeastOnce())->method('getTransports')->willReturn(new ArrayCollection([$transportMock]));

        $independentTransportVisibilityCalculationMock = $this
            ->getMock(IndependentTransportVisibilityCalculation::class, ['isIndependentlyVisible'], [], '', false);
        $independentTransportVisibilityCalculationMock
            ->expects($this->atLeastOnce())
            ->method('isIndependentlyVisible')
            ->with($this->equalTo($transportMock), $this->equalTo($domainId))
            ->willReturn(true);

        $independentPaymentVisibilityCalculationMock = $this
            ->getMock(IndependentPaymentVisibilityCalculation::class, ['isIndependentlyVisible'], [], '', false);
        $independentPaymentVisibilityCalculationMock
            ->expects($this->atLeastOnce())
            ->method('isIndependentlyVisible')
            ->with($this->equalTo($paymentMock), $this->equalTo($domainId))
            ->willReturn(true);

        $transportVisibilityCalculation = new TransportVisibilityCalculation(
            $independentTransportVisibilityCalculationMock,
            $independentPaymentVisibilityCalculationMock
        );

        $this->assertTrue($transportVisibilityCalculation->isVisible($transportMock, [$paymentMock], $domainId));
    }

    public function testFilterVisible()
    {
        $domainId = 1;
        $transportHiddenMock = $this->getMock(Transport::class, [], [], '', false);
        $transportVisibleMock = $this->getMock(Transport::class, [], [], '', false);
        $paymentMock = $this->getMock(Payment::class, ['getTransports'], [], '', false);
        $paymentMock->expects($this->atLeastOnce())->method('getTransports')->willReturn(new ArrayCollection([$transportVisibleMock]));

        $independentTransportVisibilityCalculationMock = $this
            ->getMock(IndependentTransportVisibilityCalculation::class, ['isIndependentlyVisible'], [], '', false);
        $independentTransportVisibilityCalculationMock
            ->expects($this->atLeastOnce())
            ->method('isIndependentlyVisible')
            ->with($this->equalTo($transportVisibleMock), $this->equalTo($domainId))
            ->willReturn(true);

        $independentPaymentVisibilityCalculationMock = $this
            ->getMock(IndependentPaymentVisibilityCalculation::class, ['isIndependentlyVisible'], [], '', false);
        $independentPaymentVisibilityCalculationMock
            ->expects($this->atLeastOnce())
            ->method('isIndependentlyVisible')
            ->with($this->equalTo($paymentMock), $this->equalTo($domainId))
            ->willReturn(true);

        $transportVisibilityCalculation = new TransportVisibilityCalculation(
            $independentTransportVisibilityCalculationMock,
            $independentPaymentVisibilityCalculationMock
        );

        $transports = [$transportHiddenMock, $transportVisibleMock];

        $filteredTransports = $transportVisibilityCalculation->filterVisible($transports, [$paymentMock], $domainId);

        $this->assertCount(1, $filteredTransports);
        $this->assertContains($transportVisibleMock, $filteredTransports);
    }
}
