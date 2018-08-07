<?php

namespace Shopsys\FrameworkBundle\Model\Administrator;

class AdministratorGridLimitFactory implements AdministratorGridLimitFactoryInterface
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\Administrator\Administrator $administrator
     * @param string $gridId
     * @param int $limit
     * @return \Shopsys\FrameworkBundle\Model\Administrator\AdministratorGridLimit
     */
    public function create(Administrator $administrator, string $gridId, int $limit): AdministratorGridLimit
    {
        return new AdministratorGridLimit($administrator, $gridId, $limit);
    }
}
