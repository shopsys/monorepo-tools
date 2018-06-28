<?php

namespace Shopsys\FrameworkBundle\Model\Script;

interface ScriptDataFactoryInterface
{
    /**
     * @return \Shopsys\FrameworkBundle\Model\Script\ScriptData
     */
    public function create(): ScriptData;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Script\Script $script
     * @return \Shopsys\FrameworkBundle\Model\Script\ScriptData
     */
    public function createFromScript(Script $script): ScriptData;
}
