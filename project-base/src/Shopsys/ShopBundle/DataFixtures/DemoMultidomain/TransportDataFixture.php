<?php

namespace Shopsys\ShopBundle\DataFixtures\DemoMultidomain;

use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\ShopBundle\DataFixtures\Base\CurrencyDataFixture;
use Shopsys\ShopBundle\DataFixtures\Demo\TransportDataFixture as DemoTransportDataFixture;
use Shopsys\ShopBundle\Model\Transport\TransportEditData;
use Shopsys\ShopBundle\Model\Transport\TransportEditDataFactory;
use Shopsys\ShopBundle\Model\Transport\TransportFacade;

class TransportDataFixture extends AbstractReferenceFixture
{
    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $transportEditDataFactory = $this->get(TransportEditDataFactory::class);
        /* @var $transportEditDataFactory \Shopsys\ShopBundle\Model\Transport\TransportEditDataFactory */
        $transportFacade = $this->get(TransportFacade::class);
        /* @var $transportFacade \Shopsys\ShopBundle\Model\Transport\TransportFacade */

        $currencyEur = $this->getReference(CurrencyDataFixture::CURRENCY_EUR);
        /* @var $currencyEur \Shopsys\ShopBundle\Model\Pricing\Currency\Currency */

        $transport = $this->getReference(DemoTransportDataFixture::TRANSPORT_CZECH_POST);
        $transportEditData = $transportEditDataFactory->createFromTransport($transport);
        $transportEditData->transportData->name['en'] = 'Czech post';
        $transportEditData->transportData->description['en'] = 'Only if you are crazy';
        $transportEditData->transportData->instructions['en'] = '<b>Warning!</b> Use Czech Post only if you are crazy.';
        $transportEditData->prices[$currencyEur->getId()] = 3.95;
        $transportFacade->edit($transport, $transportEditData);

        $transport = $this->getReference(DemoTransportDataFixture::TRANSPORT_PPL);
        $transportEditData = $transportEditDataFactory->createFromTransport($transport);
        $transportEditData->transportData->name['en'] = 'PPL';
        $transportEditData->prices[$currencyEur->getId()] = 6.95;
        $transportFacade->edit($transport, $transportEditData);

        $transport = $this->getReference(DemoTransportDataFixture::TRANSPORT_PERSONAL);
        $transportEditData = $transportEditDataFactory->createFromTransport($transport);
        $transportEditData->transportData->name['en'] = 'Personal takeover';
        $transportEditData->transportData->description['en'] = 'You will be welcomed friendly staff!';
        $transportEditData->prices[$currencyEur->getId()] = 0;
        $transportFacade->edit($transport, $transportEditData);
    }
}
