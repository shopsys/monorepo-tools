<?php

namespace Shopsys\ShopBundle\Tests\Unit\Component\Cron;

use DateTime;
use PHPUnit_Framework_TestCase;
use Shopsys\ShopBundle\Component\Cron\CronTimeInterface;
use Shopsys\ShopBundle\Component\Cron\CronTimeResolver;

class CronTimeResolverTest extends PHPUnit_Framework_TestCase
{

    public function validTimeStringProvider() {
        return [
            ['*', 1, 1],
            ['*', 100, 10],
            ['100', 100, 100],
            ['100', 100, 10],
            ['1', 100, 1],
            ['*/10', 100, 10],
            ['10,20,*,*/20', 100, 10],
        ];
    }

    /**
     * @dataProvider validTimeStringProvider
     */
    public function testValidateTimeString($timeString, $maxValue, $divisibleBy) {
        $cronTimeResolver = new CronTimeResolver();
        $cronTimeResolver->validateTimeString($timeString, $maxValue, $divisibleBy);
    }

    public function invalidTimeStringProvider() {
        return [
            ['abcd', 1, 1],
            ['101', 100, 10],
            ['11', 100, 10],
            ['*/11', 100, 10],
            ['*/101', 100, 10],
            ['*,*/101', 100, 10],
            ['10,20,*/11', 100, 10],
        ];
    }

    /**
     * @dataProvider invalidTimeStringProvider
     */
    public function testValidateTimeStringInvalidTimeFormatException($invalidTimeString, $maxValue, $divisibleBy) {
        $cronTimeResolver = new CronTimeResolver();
        $this->setExpectedException(\Shopsys\ShopBundle\Component\Cron\Config\Exception\InvalidTimeFormatException::class);
        $cronTimeResolver->validateTimeString($invalidTimeString, $maxValue, $divisibleBy);
    }

    public function isValidAtTimeProvider() {
        return [
            ['0', '0', '2000-01-01 00:00:00', true],
            ['1', '*', '2000-01-01 01:12:00', true],
            ['*', '*', '2000-01-01 12:12:00', true],
            ['2,3', '*', '2000-01-01 02:12:00', true],
            ['*', '1,2', '2000-01-01 00:02:00', true],
            ['*', '*/15', '2000-01-01 00:00:00', true],
            ['*', '*/15', '2000-01-01 00:15:00', true],
            ['*', '*/15', '2000-01-01 00:30:00', true],
            ['*', '*/15,*/3', '2000-01-01 00:06:00', true],
            ['*/4', '*/15', '2000-01-01 08:00:00', true],
            ['1', '*', '2000-01-01 02:00:00', false],
            ['*', '0', '2000-01-01 00:01:00', false],
            ['*', '1,3', '2000-01-01 00:02:00', false],
            ['*', '*/10', '2000-01-01 00:15:00', false],
            ['*/4', '*', '2000-01-01 02:00:00', false],
        ];
    }

    /**
     * @dataProvider isValidAtTimeProvider
     */
    public function testIsValidAtTime($timeHours, $timeMinutes, $dateTimeString, $isValid) {
        $cronTimeMock = $this->getMockBuilder(CronTimeInterface::class)
            ->setMethods(['getTimeHours', 'getTimeMinutes'])
            ->getMockForAbstractClass();
        $cronTimeMock->expects($this->any())->method('getTimeHours')->willReturn($timeHours);
        $cronTimeMock->expects($this->any())->method('getTimeMinutes')->willReturn($timeMinutes);

        $cronTimeResolver = new CronTimeResolver();

        $this->assertSame($isValid, $cronTimeResolver->isValidAtTime($cronTimeMock, new DateTime($dateTimeString)));
    }

}
