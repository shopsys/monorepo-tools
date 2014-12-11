<?php

namespace SS6\ShopBundle\Tests\Model\Transport;

use Doctrine\Common\Collections\ArrayCollection;
use SS6\ShopBundle\Model\Payment\Payment;
use SS6\ShopBundle\Model\Transport\Transport;
use SS6\ShopBundle\Model\Transport\TransportDomain;
use SS6\ShopBundle\Model\Transport\TransportRepository;
use SS6\ShopBundle\Model\Transport\TransportVisibilityCalculation;

class TransportVisibilityCalculationTest {

	public function testIsVisibleHiddenTransport() {
		$transportRepositoryMock = $this->getMock(TransportRepository::class, [], [], '', false);
		$transportMock = $this->getMock(Transport::class, ['isHidden'], [], '', false);
		$transportMock->expects($this->once())->method('isHidden')->willReturn(true);

		$visibilityCalculation = new TransportVisibilityCalculation($transportRepositoryMock);

		$this->assertFalse($visibilityCalculation->isVisible($transportMock, [], 1));
	}

	public function testIsVisibleWithoutPayments() {
		$transportRepositoryMock = $this->getMock(TransportRepository::class, [], [], '', false);
		$transportMock = $this->getMock(Transport::class, ['isHidden'], [], '', false);
		$transportMock->expects($this->once())->method('isHidden')->willReturn(false);

		$paymentMock = $this->getMock(Payment::class, ['getTransports', 'isHidden'], [], '', false);
		$paymentMock->expects($this->once())->method('getTransports')->willReturn(new ArrayCollection());
		$paymentMock->expects($this->any())->method('isHidden')->willReturn(false);

		$visibilityCalculation = new TransportVisibilityCalculation($transportRepositoryMock);

		$this->assertFalse($visibilityCalculation->isVisible($transportMock, [$paymentMock], 1));
	}

	public function testIsVisibleWithPaymentsAndWithoutDomain() {
		$transportMock = $this->getMock(Transport::class, ['isHidden'], [], '', false);
		$transportMock->expects($this->once())->method('isHidden')->willReturn(false);

		$transportDomain = new TransportDomain($transportMock, 2);

		$transportRepositoryMock = $this->getMock(TransportRepository::class, ['getTransportDomainsByTransport'], [], '', false);
		$transportRepositoryMock
			->expects($this->once())
			->method('getTransportDomainsByTransport')
			->with($this->equalTo($transportMock))
			->willReturn([$transportDomain]);

		$paymentMock1 = $this->getMock(Payment::class, ['getTransports', 'isHidden'], [], '', false);
		$paymentMock1->expects($this->once())->method('getTransports')->willReturn(new ArrayCollection());
		$paymentMock1->expects($this->any())->method('isHidden')->willReturn(false);

		$paymentMock2 = $this->getMock(Payment::class, ['getTransports', 'isHidden'], [], '', false);
		$paymentMock2->expects($this->once())->method('getTransports')->willReturn(new ArrayCollection([$transportMock]));
		$paymentMock2->expects($this->any())->method('isHidden')->willReturn(false);

		$payments = [$paymentMock1, $paymentMock2];

		$visibilityCalculation = new TransportVisibilityCalculation($transportRepositoryMock);

		$this->assertFalse($visibilityCalculation->isVisible($transportMock, $payments, 1));
	}

	public function testIsVisibleWithPaymentsAndWithDomain() {
		$transportMock = $this->getMock(Transport::class, ['isHidden'], [], '', false);
		$transportMock->expects($this->once())->method('isHidden')->willReturn(false);

		$wrongTransportDomain = new TransportDomain($transportMock, 2);
		$transportDomain = new TransportDomain($transportMock, 1);

		$transportRepositoryMock = $this->getMock(TransportRepository::class, ['getTransportDomainsByTransport'], [], '', false);
		$transportRepositoryMock
			->expects($this->once())
			->method('getTransportDomainsByTransport')
			->with($this->equalTo($transportMock))
			->willReturn([$wrongTransportDomain, $transportDomain]);

		$paymentMock1 = $this->getMock(Payment::class, ['getTransports', 'isHidden'], [], '', false);
		$paymentMock1->expects($this->once())->method('getTransports')->willReturn(new ArrayCollection());
		$paymentMock1->expects($this->any())->method('isHidden')->willReturn(false);

		$paymentMock2 = $this->getMock(Payment::class, ['getTransports', 'isHidden'], [], '', false);
		$paymentMock2->expects($this->once())->method('getTransports')->willReturn(new ArrayCollection([$transportMock]));
		$paymentMock2->expects($this->any())->method('isHidden')->willReturn(false);

		$payments = [$paymentMock1, $paymentMock2];

		$visibilityCalculation = new TransportVisibilityCalculation($transportRepositoryMock);

		$this->assertTrue($visibilityCalculation->isVisible($transportMock, $payments, 1));
	}
}
