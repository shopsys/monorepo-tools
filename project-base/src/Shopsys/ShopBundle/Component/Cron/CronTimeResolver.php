<?php

namespace Shopsys\ShopBundle\Component\Cron;

use DateTimeInterface;
use Shopsys\ShopBundle\Component\Cron\CronTimeInterface;

class CronTimeResolver
{
    /**
     * @param \Shopsys\ShopBundle\Component\Cron\CronTimeInterface $cronTime
     * @param \DateTimeInterface $dateTime
     * @return bool
     */
    public function isValidAtTime(CronTimeInterface $cronTime, DateTimeInterface $dateTime) {
        $hour = (int)$dateTime->format('G');
        $minute = (int)$dateTime->format('i');

        return
            $this->isMatchWithTimeString($hour, $cronTime->getTimeHours()) &&
            $this->isMatchWithTimeString($minute, $cronTime->getTimeMinutes());
    }

    /**
     * @param int $value
     * @param string $timeString
     * @return bool
     */
    private function isMatchWithTimeString($value, $timeString) {
        $timeValues = explode(',', $timeString);
        $matches = null;
        foreach ($timeValues as $timeValue) {
            if (
                $timeValue === '*'
                || $timeValue === (string)$value
                || preg_match('@^\*/(\d+)$@', $timeValue, $matches) && $value % $matches[1] === 0 // syntax */[int]
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $timeString
     * @param int $maxValue
     * @param int $divisibleBy
     */
    public function validateTimeString($timeString, $maxValue, $divisibleBy) {
        $timeValues = explode(',', $timeString);
        $matches = null;
        foreach ($timeValues as $timeValue) {
            // syntax */[int]
            if (preg_match('@^\*/(\d+)$@', $timeValue, $matches)) {
                $timeNumber = $matches[1];
            } else {
                $timeNumber = $timeValue;
            }

            if (
                $timeNumber !== '*'
                && !(is_numeric($timeNumber) && $timeNumber <= $maxValue && $timeNumber % $divisibleBy === 0)
            ) {
                throw new \Shopsys\ShopBundle\Component\Cron\Config\Exception\InvalidTimeFormatException($timeValue, $maxValue, $divisibleBy);
            }
        }
    }
}
