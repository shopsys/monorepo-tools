<?php

namespace Shopsys\FrameworkBundle\Model\Script;

interface ScriptFactoryInterface
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\Script\ScriptData $data
     * @return \Shopsys\FrameworkBundle\Model\Script\Script
     */
    public function create(ScriptData $data): Script;
}
