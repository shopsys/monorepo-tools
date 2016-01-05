<?php

namespace SS6\ShopBundle\Tests\Unit\Component\Cron;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Component\Cron\Config\CronModuleConfig;
use SS6\ShopBundle\Component\Cron\CronModuleExecutor;
use SS6\ShopBundle\Component\Cron\IteratedCronModuleInterface;
use Symfony\Bridge\Monolog\Logger;

class CronModuleExecutorTest extends PHPUnit_Framework_TestCase {

	public function testRunModuleSuspendAfterTimeout() {
		$cronModuleServiceMock = $this->getMockForAbstractClass(IteratedCronModuleInterface::class);
		$cronModuleServiceMock->method('iterate')->willReturnCallback(function () {
			usleep(1000);
			return true;
		});
		$cronModuleConfig = new CronModuleConfig($cronModuleServiceMock, 'moduleId', '', '');

		$loggerMock = $this->getMock(Logger::class, [], [], '', false);

		$cronModuleExecutor = new CronModuleExecutor(1);
		$this->assertSame(CronModuleExecutor::RUN_STATUS_SUSPENDED, $cronModuleExecutor->runModule($loggerMock, $cronModuleConfig));
	}

	public function testRunModuleAfterTimeout() {
		$cronModuleServiceMock = $this->getMockForAbstractClass(IteratedCronModuleInterface::class);
		$cronModuleServiceMock->expects($this->never())->method('iterate');
		$cronModuleConfig = new CronModuleConfig($cronModuleServiceMock, 'moduleId', '', '');

		$loggerMock = $this->getMock(Logger::class, [], [], '', false);

		$cronModuleExecutor = new CronModuleExecutor(1);
		sleep(1);
		$this->assertSame(CronModuleExecutor::RUN_STATUS_TIMEOUT, $cronModuleExecutor->runModule($loggerMock, $cronModuleConfig));
	}

	public function testRunModule() {
		$cronModuleServiceMock = $this->getMockForAbstractClass(IteratedCronModuleInterface::class);
		$cronModuleServiceMock->expects($this->once())->method('iterate')->willReturn(false);
		$cronModuleConfig = new CronModuleConfig($cronModuleServiceMock, 'moduleId', '', '');

		$loggerMock = $this->getMock(Logger::class, [], [], '', false);

		$cronModuleExecutor = new CronModuleExecutor(1);
		$this->assertSame(CronModuleExecutor::RUN_STATUS_OK, $cronModuleExecutor->runModule($loggerMock, $cronModuleConfig));
	}

}
