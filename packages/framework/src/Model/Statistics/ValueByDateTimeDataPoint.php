<?php

namespace Shopsys\FrameworkBundle\Model\Statistics;

use DateTime;

class ValueByDateTimeDataPoint
{
    /**
     * @var int
     */
    private $value;

    /**
     * @var \DateTime
     */
    private $dateTime;

    public function __construct($count, DateTime $dateTime)
    {
        $this->value = (int)$count;
        $this->dateTime = $dateTime;
    }

    /**
     * @return \DateTime
     */
    public function getDateTime()
    {
        return $this->dateTime;
    }

    /**
     * @return int
     */
    public function getValue()
    {
        return $this->value;
    }
}
