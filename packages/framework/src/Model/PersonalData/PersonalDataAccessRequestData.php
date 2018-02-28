<?php

namespace Shopsys\FrameworkBundle\Model\PersonalData;

use DateTime;

class PersonalDataAccessRequestData
{

    /**
     * @var DateTime
     */
    public $createAt;

    /**
     * @var string
     */
    public $email;

    /**
     * @var string
     */
    public $hash;

    /**
     * @var int
     */
    public $domainId;
}
