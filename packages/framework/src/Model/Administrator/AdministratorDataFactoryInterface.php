<?php

namespace Shopsys\FrameworkBundle\Model\Administrator;

interface AdministratorDataFactoryInterface
{
    /**
     * @return \Shopsys\FrameworkBundle\Model\Administrator\AdministratorData
     */
    public function create(): AdministratorData;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Administrator\Administrator
     * @return \Shopsys\FrameworkBundle\Model\Administrator\AdministratorData
     */
    public function createFromAdministrator(Administrator $administrator): AdministratorData;
}
