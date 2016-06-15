<?php

namespace SS6\ShopBundle\DataFixtures\DemoMultidomain;

use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use SS6\ShopBundle\DataFixtures\Base\CurrencyDataFixture;
use SS6\ShopBundle\DataFixtures\Demo\TransportDataFixture as DemoTransportDataFixture;
use SS6\ShopBundle\Model\Transport\TransportEditData;
use SS6\ShopBundle\Model\Transport\TransportEditDataFactory;
use SS6\ShopBundle\Model\Transport\TransportEditFacade;

class TransportDataFixture extends AbstractReferenceFixture {

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {
		$transportEditDataFactory = $this->get(TransportEditDataFactory::class);
		/* @var $transportEditDataFactory \SS6\ShopBundle\Model\Transport\TransportEditDataFactory */
		$transportEditFacade = $this->get(TransportEditFacade::class);
		/* @var $transportEditFacade \SS6\ShopBundle\Model\Transport\TransportEditFacade */

		$currencyEur = $this->getReference(CurrencyDataFixture::CURRENCY_EUR);
		/* @var $currencyEur \SS6\ShopBundle\Model\Pricing\Currency\Currency */

		$transport = $this->getReference(DemoTransportDataFixture::TRANSPORT_CZECH_POST);
		$transportEditData = $transportEditDataFactory->createFromTransport($transport);
		$transportEditData->transportData->name['en'] = 'Czech post';
		$transportEditData->transportData->description['en'] = 'Only if you are crazy';
		$transportEditData->transportData->instructions['en'] = '<b>Warning!</b> Use Czech Post only if you are crazy.';
		$transportEditData->prices[$currencyEur->getId()] = 3.95;
		$transportEditData->transportData->domains[] = 2;
		$transportEditFacade->edit($transport, $transportEditData);

		$transport = $this->getReference(DemoTransportDataFixture::TRANSPORT_PPL);
		$transportEditData = $transportEditDataFactory->createFromTransport($transport);
		$transportEditData->transportData->name['en'] = 'PPL';
		$transportEditData->prices[$currencyEur->getId()] = 6.95;
		$transportEditData->transportData->domains[] = 2;
		$transportEditFacade->edit($transport, $transportEditData);

		$transport = $this->getReference(DemoTransportDataFixture::TRANSPORT_PERSONAL);
		$transportEditData = $transportEditDataFactory->createFromTransport($transport);
		$transportEditData->transportData->name['en'] = 'Personal takeover';
		$transportEditData->transportData->description['en'] = 'You will be welcomed friendly staff!';
		$transportEditData->prices[$currencyEur->getId()] = 0;
		$transportEditData->transportData->domains[] = 2;
		$transportEditFacade->edit($transport, $transportEditData);
	}

}
