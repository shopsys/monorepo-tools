<?php

namespace Shopsys\ShopBundle\DataFixtures\Demo;

use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Model\Slider\SliderItemData;
use Shopsys\ShopBundle\Model\Slider\SliderItemFacade;

class SliderItemDataFixture extends AbstractReferenceFixture{

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager) {
        $sliderItemFacade = $this->get(SliderItemFacade::class);
        /* @var $sliderItemFacade \Shopsys\ShopBundle\Model\Slider\SliderItemFacade*/

        $sliderItemData = new SliderItemData();
        $sliderItemData->domainId = Domain::FIRST_DOMAIN_ID;

        $sliderItemData->name = 'Shopsys';
        $sliderItemData->link = 'http://www.shopsys.cz/';
        $sliderItemData->hidden = false;

        $sliderItemFacade->create($sliderItemData, Domain::FIRST_DOMAIN_ID);

        $sliderItemData->name = 'Twitter';
        $sliderItemData->link = 'https://twitter.com/netdevelo_cz';

        $sliderItemFacade->create($sliderItemData, Domain::FIRST_DOMAIN_ID);

        $sliderItemData->name = 'Pojďte s námi růst';
        $sliderItemData->link = 'http://www.pojdtesnamirust.cz/';

        $sliderItemFacade->create($sliderItemData, Domain::FIRST_DOMAIN_ID);
    }
}
