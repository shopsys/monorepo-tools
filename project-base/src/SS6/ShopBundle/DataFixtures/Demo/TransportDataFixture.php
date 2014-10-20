<?php

namespace SS6\ShopBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\DataFixtures\Base\VatDataFixture;
use SS6\ShopBundle\Model\DataFixture\AbstractReferenceFixture;
use SS6\ShopBundle\Model\Transport\TransportData;

class TransportDataFixture extends AbstractReferenceFixture implements DependentFixtureInterface {

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {
		$transportData = new TransportData();
		$transportData->setName('Česká pošta - balík do ruky');
		$transportData->setPrice(99.95);
		$transportData->setDescription('Pouze na vlastní nebezpečí');
		$transportData->setVat($this->getReference(VatDataFixture::VAT_HIGH));
		$transportData->setDomains(array(1));
		$transportData->setHidden(false);
		$this->createTransport('transport_cp', $transportData);

		$transportData->setName('PPL');
		$transportData->setPrice(199.95);
		$transportData->setDescription(null);
		$this->createTransport('transport_ppl', $transportData);

		$transportData->setName('Osobní převzetí');
		$transportData->setPrice(0);
		$transportData->setDescription('Uvítá Vás milý personál!');
		$transportData->setVat($this->getReference(VatDataFixture::VAT_ZERO));
		$this->createTransport('transport_personal', $transportData);
	}

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 * @param string $referenceName
	 * @param \SS6\ShopBundle\Model\Transport\TransportData $transportData
	 */
	private function createTransport($referenceName, TransportData $transportData) {
		$transportEditFacade = $this->get('ss6.shop.transport.transport_edit_facade');
		/* @var $transportEditFacade \SS6\ShopBundle\Model\Transport\TransportEditFacade */

		$transport = $transportEditFacade->create($transportData);
		$this->addReference($referenceName, $transport);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getDependencies() {
		return array(
			VatDataFixture::class,
		);
	}

}
