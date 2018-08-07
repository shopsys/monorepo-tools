<?php

namespace Shopsys\FrameworkBundle\Model\Script;

class ScriptFactory implements ScriptFactoryInterface
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\Script\ScriptData $data
     * @return \Shopsys\FrameworkBundle\Model\Script\Script
     */
    public function create(ScriptData $data): Script
    {
        return new Script($data);
    }
}
