<?php

namespace Tests\ShopBundle\Unit\Component\Cron;

use PHPUnit_Framework_TestCase;
use Shopsys\Plugin\Cron\IteratedCronModuleInterface;
use Shopsys\ShopBundle\Component\Cron\CronModuleExecutor;

class CronModuleExecutorTest extends PHPUnit_Framework_TestCase
{
    public function testRunModuleSuspendAfterTimeout()
    {
        $cronModuleServiceMock = $this->getMockForAbstractClass(IteratedCronModuleInterface::class);
        $cronModuleServiceMock->expects($this->once())->method('sleep');
        $cronModuleServiceMock->method('iterate')->willReturnCallback(function () {
            usleep(1000);
            return true;
        });

        $cronModuleExecutor = new CronModuleExecutor(1);
        $this->assertSame(
            CronModuleExecutor::RUN_STATUS_SUSPENDED,
            $cronModuleExecutor->runModule($cronModuleServiceMock, false)
        );
    }

    public function testRunModuleAfterTimeout()
    {
        $cronModuleServiceMock = $this->getMockForAbstractClass(IteratedCronModuleInterface::class);
        $cronModuleServiceMock->expects($this->never())->method('iterate');

        $cronModuleExecutor = new CronModuleExecutor(1);
        sleep(1);
        $this->assertSame(
            CronModuleExecutor::RUN_STATUS_TIMEOUT,
            $cronModuleExecutor->runModule($cronModuleServiceMock, false)
        );
    }

    public function testRunModule()
    {
        $cronModuleServiceMock = $this->getMockForAbstractClass(IteratedCronModuleInterface::class);
        $cronModuleServiceMock->expects($this->never())->method('wakeUp');
        $cronModuleServiceMock->expects($this->once())->method('iterate')->willReturn(false);

        $cronModuleExecutor = new CronModuleExecutor(1);
        $this->assertSame(
            CronModuleExecutor::RUN_STATUS_OK,
            $cronModuleExecutor->runModule($cronModuleServiceMock, false)
        );
    }

    public function testRunSuspendedModule()
    {
        $cronModuleServiceMock = $this->getMockForAbstractClass(IteratedCronModuleInterface::class);
        $cronModuleServiceMock->expects($this->once())->method('wakeUp');
        $cronModuleServiceMock->method('iterate')->willReturn(false);

        $cronModuleExecutor = new CronModuleExecutor(1);
        $cronModuleExecutor->runModule($cronModuleServiceMock, true);
    }
}
