<?php

namespace Tests\ShopBundle\Unit\Component\Cron;

use DateTime;
use PHPUnit\Framework\Assert;
use PHPUnit_Framework_TestCase;
use Shopsys\ShopBundle\Component\Cron\Config\CronConfig;
use Shopsys\ShopBundle\Component\Cron\Config\CronModuleConfig;
use Shopsys\ShopBundle\Component\Cron\CronFacade;
use Shopsys\ShopBundle\Component\Cron\CronModuleFacade;
use Shopsys\ShopBundle\Component\Cron\CronTimeResolver;
use Shopsys\ShopBundle\Component\Cron\IteratedCronModuleInterface;
use Shopsys\ShopBundle\Component\Cron\SimpleCronModuleInterface;
use Symfony\Bridge\Monolog\Logger;

class CronFacadeTest extends PHPUnit_Framework_TestCase
{
    public function testRunModuleByServiceId()
    {
        $serviceId = 'cronModuleServiceId';
        $cronModuleFacadeMock = $this->mockCronModuleFacade();
        $cronModuleServiceMock = $this->getMockForAbstractClass(SimpleCronModuleInterface::class);

        $cronModuleServiceMock->expects($this->once())->method('run');
        $this->expectMethodCallWithCronModuleConfig($cronModuleFacadeMock, 'unscheduleModule', $serviceId);

        $cronConfig = $this->createCronConfigWithRegisteredServices([
            $serviceId => $cronModuleServiceMock,
        ]);
        $this->createCronFacade($cronConfig, $cronModuleFacadeMock)->runModuleByServiceId($serviceId);
    }

    public function testRunIteratedModuleByServiceId()
    {
        $serviceId = 'cronModuleServiceId';
        $cronModuleFacadeMock = $this->mockCronModuleFacade();
        $cronModuleServiceMock = $this->getMockForAbstractClass(IteratedCronModuleInterface::class);

        $iterations = 3;
        $cronModuleServiceMock->expects($this->exactly($iterations))->method('iterate')->willReturnCallback(
            function () use (&$iterations) {
                $iterations--;
                return $iterations > 0;
            }
        );
        $this->expectMethodCallWithCronModuleConfig($cronModuleFacadeMock, 'unscheduleModule', $serviceId);

        $cronConfig = $this->createCronConfigWithRegisteredServices([
            $serviceId => $cronModuleServiceMock,
        ]);
        $this->createCronFacade($cronConfig, $cronModuleFacadeMock)->runModuleByServiceId($serviceId);
    }

    public function testScheduleModulesByTime()
    {
        $validServiceId = 'validCronModuleServiceId';
        $validCronModuleServiceMock = $this->getMockForAbstractClass(SimpleCronModuleInterface::class);
        $invalidServiceId = 'invalidCronModuleServiceId';
        $invalidCronModuleServiceMock = $this->getMockForAbstractClass(SimpleCronModuleInterface::class);
        $cronModuleFacadeMock = $this->mockCronModuleFacade();

        $cronTimeResolverMock = $this->createMock(CronTimeResolver::class);
        $cronTimeResolverMock->method('isValidAtTime')->willReturnCallback(
            function (CronModuleConfig $cronModuleConfig) use ($validServiceId) {
                return $cronModuleConfig->getServiceId() === $validServiceId;
            }
        );

        $cronModuleFacadeMock->expects($this->atLeastOnce())
            ->method('scheduleModules')
            ->with(Assert::callback(function ($modules) use ($validServiceId) {
                return count($modules) === 1 && current($modules)->getServiceId() === $validServiceId;
            }));

        $cronConfig = $this->createCronConfigWithRegisteredServices([
            $validServiceId => $validCronModuleServiceMock,
            $invalidServiceId => $invalidCronModuleServiceMock,
        ], $cronTimeResolverMock);
        $this->createCronFacade($cronConfig, $cronModuleFacadeMock)->scheduleModulesByTime(new DateTime());
    }

    public function testRunScheduledModules()
    {
        $scheduledServiceId = 'scheduledCronModuleServiceId';
        $scheduledCronModuleServiceMock = $this->getMockForAbstractClass(SimpleCronModuleInterface::class);
        $unscheduledServiceId = 'unscheduledCronModuleServiceId';
        $unscheduledCronModuleServiceMock = $this->getMockForAbstractClass(SimpleCronModuleInterface::class);
        $cronModuleFacadeMock = $this->mockCronModuleFacade();

        $scheduledCronModuleServiceMock->expects($this->once())->method('run');
        $unscheduledCronModuleServiceMock->expects($this->never())->method('run');

        $cronConfig = $this->createCronConfigWithRegisteredServices([
            $scheduledServiceId => $scheduledCronModuleServiceMock,
            $unscheduledServiceId => $unscheduledCronModuleServiceMock,
        ]);
        $cronModuleFacadeMock
            ->method('getOnlyScheduledCronModuleConfigs')
            ->willReturnCallback(function () use ($scheduledServiceId, $scheduledCronModuleServiceMock) {
                return [new CronModuleConfig($scheduledCronModuleServiceMock, $scheduledServiceId, '*', '*')];
            });
        $this->expectMethodCallWithCronModuleConfig($cronModuleFacadeMock, 'unscheduleModule', $scheduledServiceId);

        $this->createCronFacade($cronConfig, $cronModuleFacadeMock)->runScheduledModules();
    }

    /**
     * @param \Shopsys\ShopBundle\Component\Cron\Config\CronConfig $cronConfig
     * @param \Shopsys\ShopBundle\Component\Cron\CronModuleFacade $cronModuleFacade
     * @return \Shopsys\ShopBundle\Component\Cron\CronFacade
     */
    private function createCronFacade(CronConfig $cronConfig, CronModuleFacade $cronModuleFacade)
    {
        $loggerMock = $this->createMock(Logger::class);
        /* @var $loggerMock \Symfony\Bridge\Monolog\Logger */

        return new CronFacade($loggerMock, $cronConfig, $cronModuleFacade);
    }

    /**
     * @return \Shopsys\ShopBundle\Component\Cron\CronModuleFacade|\PHPUnit_Framework_MockObject_MockObject
     */
    private function mockCronModuleFacade()
    {
        return $this->createMock(CronModuleFacade::class);
    }

    /**
     * @param \PHPUnit_Framework_MockObject_MockObject $mock
     * @param string $methodName
     * @param string $serviceId
     */
    private function expectMethodCallWithCronModuleConfig($mock, $methodName, $serviceId)
    {
        $mock->expects($this->atLeastOnce())
            ->method($methodName)
            ->with($this->attributeEqualTo('serviceId', $serviceId));
    }

    /**
     * @param array $servicesIndexedById
     * @param \Shopsys\ShopBundle\Component\Cron\CronTimeResolver|\PHPUnit_Framework_MockObject_MockObject|null $cronTimeResolverMock
     * @return \Shopsys\ShopBundle\Component\Cron\Config\CronConfig
     */
    private function createCronConfigWithRegisteredServices(array $servicesIndexedById, $cronTimeResolverMock = null)
    {
        $cronTimeResolver = $cronTimeResolverMock !== null ? $cronTimeResolverMock : new CronTimeResolver();
        $cronConfig = new CronConfig($cronTimeResolver);
        foreach ($servicesIndexedById as $serviceId => $service) {
            $cronConfig->registerCronModule($service, $serviceId, '*', '*');
        }

        return $cronConfig;
    }
}
