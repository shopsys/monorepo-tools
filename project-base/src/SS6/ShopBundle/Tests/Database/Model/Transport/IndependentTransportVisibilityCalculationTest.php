<?php

namespace SS6\ShopBundle\Tests\Database\Model\Transport;

use SS6\ShopBundle\Model\Pricing\Vat\Vat;
use SS6\ShopBundle\Model\Pricing\Vat\VatData;
use SS6\ShopBundle\Model\Transport\IndependentTransportVisibilityCalculation;
use SS6\ShopBundle\Model\Transport\Transport;
use SS6\ShopBundle\Model\Transport\TransportData;
use SS6\ShopBundle\Model\Transport\TransportDomain;
use SS6\ShopBundle\Tests\Test\DatabaseTestCase;

/**
 * @UglyTest
 */
class IndependentTransportVisibilityCalculationTest extends DatabaseTestCase {

	public function testIsIndependentlyVisible() {
		$em = $this->getEntityManager();

		$domainId = 1;
		$vat = new Vat(new VatData('vat', 21));
		$transport = new Transport(new TransportData(['cs' => 'transportName', 'en' => 'transportName'], $vat, [], [], false));

		$em->persist($vat);
		$em->persist($transport);
		$em->flush();

		$transportDomain = new TransportDomain($transport, $domainId);
		$em->persist($transportDomain);
		$em->flush();

		$independentTransportVisibilityCalculation =
			$this->getContainer()->get(IndependentTransportVisibilityCalculation::class);
		/* @var $independentTransportVisibilityCalculation \SS6\ShopBundle\Model\Transport\IndependentTransportVisibilityCalculation */

		$this->assertTrue($independentTransportVisibilityCalculation->isIndependentlyVisible($transport, $domainId));
	}

	public function testIsIndependentlyVisibleEmptyName() {
		$em = $this->getEntityManager();

		$domainId = 1;
		$vat = new Vat(new VatData('vat', 21));
		$transport = new Transport(new TransportData(['cs' => null], $vat, [], [], false));

		$em->persist($vat);
		$em->persist($transport);
		$em->flush();

		$transportDomain = new TransportDomain($transport, $domainId);
		$em->persist($transportDomain);
		$em->flush();

		$independentTransportVisibilityCalculation =
			$this->getContainer()->get(IndependentTransportVisibilityCalculation::class);
		/* @var $independentTransportVisibilityCalculation \SS6\ShopBundle\Model\Transport\IndependentTransportVisibilityCalculation */

		$this->assertFalse($independentTransportVisibilityCalculation->isIndependentlyVisible($transport, $domainId));
	}

	public function testIsIndependentlyVisibleNotOnDomain() {
		$em = $this->getEntityManager();

		$domainId = 1;
		$vat = new Vat(new VatData('vat', 21));
		$transport = new Transport(new TransportData(['cs' => 'transportName'], $vat, [], [], false));

		$em->persist($vat);
		$em->persist($transport);
		$em->flush();

		$independentTransportVisibilityCalculation =
			$this->getContainer()->get(IndependentTransportVisibilityCalculation::class);
		/* @var $independentTransportVisibilityCalculation \SS6\ShopBundle\Model\Transport\IndependentTransportVisibilityCalculation */

		$this->assertFalse($independentTransportVisibilityCalculation->isIndependentlyVisible($transport, $domainId));
	}

	public function testIsIndependentlyVisibleHidden() {
		$em = $this->getEntityManager();

		$domainId = 1;
		$vat = new Vat(new VatData('vat', 21));
		$transport = new Transport(new TransportData(['cs' => 'transportName'], $vat, [], [], true));

		$em->persist($vat);
		$em->persist($transport);
		$em->flush();

		$transportDomain = new TransportDomain($transport, $domainId);
		$em->persist($transportDomain);
		$em->flush();

		$independentTransportVisibilityCalculation =
			$this->getContainer()->get(IndependentTransportVisibilityCalculation::class);
		/* @var $independentTransportVisibilityCalculation \SS6\ShopBundle\Model\Transport\IndependentTransportVisibilityCalculation */

		$this->assertFalse($independentTransportVisibilityCalculation->isIndependentlyVisible($transport, $domainId));
	}

}
