<?php

namespace Shopsys\FrameworkBundle\Model\PersonalData;

use DateTime;

class PersonalDataAccessRequestData
{
    /**
     * @var DateTime|null
     */
    public $createAt;

    /**
     * @var string|null
     */
    public $email;

    /**
     * @var string|null
     */
    public $hash;

    /**
     * @var int|null
     */
    public $domainId;

    /**
     * @var string|null
     */
    public $type;
}
