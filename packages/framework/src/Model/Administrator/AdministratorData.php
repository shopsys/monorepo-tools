<?php

namespace Shopsys\FrameworkBundle\Model\Administrator;

class AdministratorData
{
    /**
     * @var bool
     */
    public $superadmin;

    /**
     * @var string|null
     */
    public $username;

    /**
     * @var string|null
     */
    public $realName;

    /**
     * @var string|null
     */
    public $password;

    /**
     * @var string|null
     */
    public $email;

    public function __construct()
    {
        $this->superadmin = false;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Administrator\Administrator $administrator
     */
    public function setFromEntity(Administrator $administrator)
    {
        $this->email = $administrator->getEmail();
        $this->realName = $administrator->getRealName();
        $this->username = $administrator->getUsername();
        $this->superadmin = $administrator->isSuperadmin();
    }
}
