<?php

namespace Tests\ShopBundle\Unit\Component\Cron;

use PHPUnit_Framework_TestCase;
use Shopsys\FrameworkBundle\Component\Cron\Config\CronModuleConfig;
use Shopsys\FrameworkBundle\Component\Cron\CronService;
use Shopsys\Plugin\Cron\SimpleCronModuleInterface;

class CronServiceTest extends PHPUnit_Framework_TestCase
{
    public function testFilterScheduledCronModuleConfigs()
    {
        $cronModuleMock = $this->getMockForAbstractClass(SimpleCronModuleInterface::class);

        $scheduledCronModuleConfig1 = new CronModuleConfig($cronModuleMock, 'scheduled1', '', '');
        $skippedCronModuleConfig = new CronModuleConfig($cronModuleMock, 'skipped', '', '');
        $scheduledCronModuleConfig2 = new CronModuleConfig($cronModuleMock, 'scheduled2', '', '');
        $cronModuleConfigs = [
            0 => $scheduledCronModuleConfig1,
            1 => $skippedCronModuleConfig,
            2 => $scheduledCronModuleConfig2,
        ];

        $scheduledCronModuleIds = ['scheduled1', 'scheduled2'];

        $cronService = new CronService();
        $scheduledCronModuleConfigs = $cronService->filterScheduledCronModuleConfigs(
            $cronModuleConfigs,
            $scheduledCronModuleIds
        );

        $this->assertEquals(
            [
                0 => $scheduledCronModuleConfig1,
                2 => $scheduledCronModuleConfig2,
            ],
            $scheduledCronModuleConfigs
        );
    }
}
