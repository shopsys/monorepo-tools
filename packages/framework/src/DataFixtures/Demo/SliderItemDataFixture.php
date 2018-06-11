<?php

namespace Shopsys\FrameworkBundle\DataFixtures\Demo;

use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\FrameworkBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Model\Slider\SliderItemData;
use Shopsys\FrameworkBundle\Model\Slider\SliderItemFacade;

class SliderItemDataFixture extends AbstractReferenceFixture
{
    /** @var \Shopsys\FrameworkBundle\Model\Slider\SliderItemFacade */
    private $sliderItemFacade;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Slider\SliderItemFacade $sliderItemFacade
     */
    public function __construct(SliderItemFacade $sliderItemFacade)
    {
        $this->sliderItemFacade = $sliderItemFacade;
    }

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $sliderItemData = new SliderItemData();
        $sliderItemData->domainId = Domain::FIRST_DOMAIN_ID;

        $sliderItemData->name = 'Shopsys';
        $sliderItemData->link = 'http://www.shopsys.cz/';
        $sliderItemData->hidden = false;

        $this->sliderItemFacade->create($sliderItemData);

        $sliderItemData->name = 'Twitter';
        $sliderItemData->link = 'https://twitter.com/shopsys';

        $this->sliderItemFacade->create($sliderItemData);

        $sliderItemData->name = 'Pojďte s námi růst';
        $sliderItemData->link = 'http://www.pojdtesnamirust.cz/';

        $this->sliderItemFacade->create($sliderItemData);
    }
}
