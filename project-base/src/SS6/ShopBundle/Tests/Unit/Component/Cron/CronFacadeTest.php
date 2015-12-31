<?php

namespace SS6\ShopBundle\Tests\Unit\Component\Cron;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Component\Cron\Config\CronConfig;
use SS6\ShopBundle\Component\Cron\Config\CronModuleConfig;
use SS6\ShopBundle\Component\Cron\CronFacade;
use SS6\ShopBundle\Component\Cron\CronModuleInterface;
use SS6\ShopBundle\Component\Cron\CronTimeResolver;
use SS6\ShopBundle\Component\Cron\IteratedCronModuleInterface;
use Symfony\Bridge\Monolog\Logger;

class CronFacadeTest extends PHPUnit_Framework_TestCase {

	public function testRunModuleByModuleId() {
		$moduleId = 'moduleId';
		$cronModuleServiceMock = $this->getMockForAbstractClass(CronModuleInterface::class);
		$cronModuleServiceMock->expects($this->once())->method('run');
		$cronModuleConfig = new CronModuleConfig($cronModuleServiceMock, $moduleId, '', '');
		$cronTimeResolver = new CronTimeResolver();
		$cronConfig = new CronConfig($cronTimeResolver, [$cronModuleConfig]);

		$loggerMock = $this->getMock(Logger::class, [], [], '', false);
		$cronFacade = new CronFacade($loggerMock, $cronConfig);
		$cronFacade->runModuleByModuleId($moduleId);
	}

	public function testRunIterableModuleByModuleId() {
		$moduleId = 'moduleId';
		$cronModuleServiceMock = $this->getMockForAbstractClass(IteratedCronModuleInterface::class);
		$cronModuleServiceMock->expects($this->once())->method('initialize');
		$iterations = 3;
		$cronModuleServiceMock->expects($this->exactly($iterations))->method('iterate')->willReturnCallback(
			function () use (&$iterations) {
				$iterations--;
				return $iterations > 0;
			}
		);
		$cronModuleConfig = new CronModuleConfig($cronModuleServiceMock, $moduleId, '', '');
		$cronTimeResolver = new CronTimeResolver();
		$cronConfig = new CronConfig($cronTimeResolver, [$cronModuleConfig]);
		$loggerMock = $this->getMock(Logger::class, [], [], '', false);

		$cronFacade = new CronFacade($loggerMock, $cronConfig);
		$cronFacade->runModuleByModuleId($moduleId);
	}

}
