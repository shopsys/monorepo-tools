<?php

namespace Shopsys\FrameworkBundle\Model\Administrator\Activity;

use Shopsys\FrameworkBundle\Model\Administrator\Administrator;

class AdministratorActivityFactory implements AdministratorActivityFactoryInterface
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\Administrator\Administrator $administrator
     * @param string $ipAddress
     * @return \Shopsys\FrameworkBundle\Model\Administrator\Activity\AdministratorActivity
     */
    public function create(Administrator $administrator, string $ipAddress): AdministratorActivity
    {
        return new AdministratorActivity($administrator, $ipAddress);
    }
}
