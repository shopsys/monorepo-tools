<?php

namespace Shopsys\ShopBundle\DataFixtures\Base;

use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\ShopBundle\Model\Product\Flag\FlagData;
use Shopsys\ShopBundle\Model\Product\Flag\FlagFacade;

class FlagDataFixture extends AbstractReferenceFixture
{
    const FLAG_NEW_PRODUCT = 'flag_new_product';
    const FLAG_TOP_PRODUCT = 'flag_top_product';
    const FLAG_ACTION_PRODUCT = 'flag_action';

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $flagData = new FlagData();

        $flagData->name = ['cs' => 'Novinka', 'en' => 'New'];
        $flagData->rgbColor = '#efd6ff';
        $flagData->visible = true;
        $this->createFlag($flagData, self::FLAG_NEW_PRODUCT);

        $flagData->name = ['cs' => 'Nejprodávanější', 'en' => 'TOP'];
        $flagData->rgbColor = '#d6fffa';
        $flagData->visible = true;
        $this->createFlag($flagData, self::FLAG_TOP_PRODUCT);

        $flagData->name = ['cs' => 'Akce', 'en' => 'Action'];
        $flagData->rgbColor = '#f9ffd6';
        $flagData->visible = true;
        $this->createFlag($flagData, self::FLAG_ACTION_PRODUCT);
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Flag\FlagData $flagData
     * @param string|null $referenceName
     */
    private function createFlag(FlagData $flagData, $referenceName = null)
    {
        $flagFacade = $this->get('shopsys.shop.product.flag.flag_facade');
        /* @var $flagFacade \Shopsys\ShopBundle\Model\Product\Flag\FlagFacade */

        $flag = $flagFacade->create($flagData);
        if ($referenceName !== null) {
            $this->addReference($referenceName, $flag);
        }
    }
}
