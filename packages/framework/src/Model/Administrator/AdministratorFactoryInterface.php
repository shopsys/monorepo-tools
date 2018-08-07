<?php

namespace Shopsys\FrameworkBundle\Model\Administrator;

interface AdministratorFactoryInterface
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\Administrator\AdministratorData $data
     * @return \Shopsys\FrameworkBundle\Model\Administrator\Administrator
     */
    public function create(AdministratorData $data): Administrator;
}
