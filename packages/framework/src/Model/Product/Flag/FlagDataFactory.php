<?php

namespace Shopsys\FrameworkBundle\Model\Product\Flag;

class FlagDataFactory implements FlagDataFactoryInterface
{
    /**
     * @return \Shopsys\FrameworkBundle\Model\Product\Flag\FlagData
     */
    public function create(): FlagData
    {
        return new FlagData();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Flag\Flag $flag
     * @return \Shopsys\FrameworkBundle\Model\Product\Flag\FlagData
     */
    public function createFromFlag(Flag $flag): FlagData
    {
        $flagData = new FlagData();
        $this->fillFromFlag($flagData, $flag);

        return $flagData;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Flag\FlagData $flagData
     * @param \Shopsys\FrameworkBundle\Model\Product\Flag\Flag $flag
     */
    protected function fillFromFlag(FlagData $flagData, Flag $flag)
    {
        $translations = $flag->getTranslations();
        $names = [];
        foreach ($translations as $translate) {
            $names[$translate->getLocale()] = $translate->getName();
        }
        $flagData->name = $names;
        $flagData->rgbColor = $flag->getRgbColor();
        $flagData->visible = $flag->isVisible();
    }
}
