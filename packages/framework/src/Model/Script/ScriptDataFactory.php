<?php

namespace Shopsys\FrameworkBundle\Model\Script;

class ScriptDataFactory implements ScriptDataFactoryInterface
{
    /**
     * @return \Shopsys\FrameworkBundle\Model\Script\ScriptData
     */
    public function create(): ScriptData
    {
        return new ScriptData();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Script\Script $script
     * @return \Shopsys\FrameworkBundle\Model\Script\ScriptData
     */
    public function createFromScript(Script $script): ScriptData
    {
        $scriptData = new ScriptData();
        $this->fillFromScript($scriptData, $script);

        return $scriptData;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Script\ScriptData $scriptData
     * @param \Shopsys\FrameworkBundle\Model\Script\Script $script
     */
    protected function fillFromScript(ScriptData $scriptData, Script $script)
    {
        $scriptData->name = $script->getName();
        $scriptData->code = $script->getCode();
        $scriptData->placement = $script->getPlacement();
    }
}
