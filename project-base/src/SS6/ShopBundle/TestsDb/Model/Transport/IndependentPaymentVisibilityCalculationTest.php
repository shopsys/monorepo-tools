<?php

namespace SS6\ShopBundle\TestsDb\Model\Transport;

use SS6\ShopBundle\Component\Test\DatabaseTestCase;
use SS6\ShopBundle\Model\Transport\Transport;
use SS6\ShopBundle\Model\Transport\TransportData;
use SS6\ShopBundle\Model\Transport\TransportDomain;
use SS6\ShopBundle\Model\Pricing\Vat\Vat;
use SS6\ShopBundle\Model\Pricing\Vat\VatData;

class IndependentTransportVisibilityCalculationTest extends DatabaseTestCase {

	public function testIsIndependentlyVisible() {
		$em = $this->getEntityManager();

		$domainId = 1;
		$vat = new Vat(new VatData('vat', 21));
		$transport = new Transport(new TransportData(['cs' => 'transportName', 'en' => 'transportName'], 0, $vat, [], [], false));

		$em->persist($vat);
		$em->persist($transport);
		$em->flush();

		$transportDomain = new TransportDomain($transport, $domainId);
		$em->persist($transportDomain);
		$em->flush();

		$independentTransportVisibilityCalculation =
			$this->getContainer()->get('ss6.shop.transport.independent_transport_visibility_calculation');
		/* @var $independentTransportVisibilityCalculation \SS6\ShopBundle\Model\Transport\IndependentTransportVisibilityCalculation */

		$this->assertTrue($independentTransportVisibilityCalculation->isIndependentlyVisible($transport, $domainId));
	}

	public function testIsIndependentlyVisibleEmptyName() {
		$em = $this->getEntityManager();

		$domainId = 2;
		$vat = new Vat(new VatData('vat', 21));
		$transport = new Transport(new TransportData(['cs' => 'transportName', 'en' => ''], 0, $vat, [], [], false));

		$em->persist($vat);
		$em->persist($transport);
		$em->flush();

		$transportDomain = new TransportDomain($transport, $domainId);
		$em->persist($transportDomain);
		$em->flush();

		$independentTransportVisibilityCalculation =
			$this->getContainer()->get('ss6.shop.transport.independent_transport_visibility_calculation');
		/* @var $independentTransportVisibilityCalculation \SS6\ShopBundle\Model\Transport\IndependentTransportVisibilityCalculation */

		$this->assertFalse($independentTransportVisibilityCalculation->isIndependentlyVisible($transport, $domainId));
	}

	public function testIsIndependentlyVisibleNotOnDomain() {
		$em = $this->getEntityManager();

		$domainId = 1;
		$diffetentDomainId = 2;
		$vat = new Vat(new VatData('vat', 21));
		$transport = new Transport(new TransportData(['cs' => 'transportName', 'en' => 'transportName'], 0, $vat, [], [], false));

		$em->persist($vat);
		$em->persist($transport);
		$em->flush();

		$transportDomain = new TransportDomain($transport, $diffetentDomainId);
		$em->persist($transportDomain);
		$em->flush();

		$independentTransportVisibilityCalculation =
			$this->getContainer()->get('ss6.shop.transport.independent_transport_visibility_calculation');
		/* @var $independentTransportVisibilityCalculation \SS6\ShopBundle\Model\Transport\IndependentTransportVisibilityCalculation */

		$this->assertFalse($independentTransportVisibilityCalculation->isIndependentlyVisible($transport, $domainId));
	}

	public function testIsIndependentlyVisibleHidden() {
		$em = $this->getEntityManager();

		$domainId = 1;
		$vat = new Vat(new VatData('vat', 21));
		$transport = new Transport(new TransportData(['cs' => 'transportName', 'en' => 'transportName'], 0, $vat, [], [], true));

		$em->persist($vat);
		$em->persist($transport);
		$em->flush();

		$transportDomain = new TransportDomain($transport, $domainId);
		$em->persist($transportDomain);
		$em->flush();

		$independentTransportVisibilityCalculation =
			$this->getContainer()->get('ss6.shop.transport.independent_transport_visibility_calculation');
		/* @var $independentTransportVisibilityCalculation \SS6\ShopBundle\Model\Transport\IndependentTransportVisibilityCalculation */

		$this->assertFalse($independentTransportVisibilityCalculation->isIndependentlyVisible($transport, $domainId));
	}

}
