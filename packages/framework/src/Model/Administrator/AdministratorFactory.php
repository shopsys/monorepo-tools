<?php

namespace Shopsys\FrameworkBundle\Model\Administrator;

class AdministratorFactory implements AdministratorFactoryInterface
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\Administrator\AdministratorData $data
     * @return \Shopsys\FrameworkBundle\Model\Administrator\Administrator
     */
    public function create(AdministratorData $data): Administrator
    {
        return new Administrator($data);
    }
}
