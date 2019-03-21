<?php

namespace Shopsys\FrameworkBundle\Component\Cron;

use DateTimeImmutable;
use Shopsys\Plugin\Cron\IteratedCronModuleInterface;
use Shopsys\Plugin\Cron\SimpleCronModuleInterface;

class CronModuleExecutor
{
    public const RUN_STATUS_OK = 'ok';
    public const RUN_STATUS_TIMEOUT = 'timeout';
    public const RUN_STATUS_SUSPENDED = 'suspended';

    /**
     * @var \DateTimeImmutable|null
     */
    protected $canRunTo;

    /**
     * @param int $secondsTimeout
     */
    public function __construct($secondsTimeout)
    {
        $this->canRunTo = new DateTimeImmutable('+' . $secondsTimeout . ' sec');
    }

    /**
     * @param \Shopsys\Plugin\Cron\SimpleCronModuleInterface|\Shopsys\Plugin\Cron\IteratedCronModuleInterface $cronModuleService
     * @param bool $suspended
     * @return string
     */
    public function runModule($cronModuleService, $suspended)
    {
        if (!$this->canRun()) {
            return self::RUN_STATUS_TIMEOUT;
        }

        if ($cronModuleService instanceof SimpleCronModuleInterface) {
            $cronModuleService->run();

            return self::RUN_STATUS_OK;
        } elseif ($cronModuleService instanceof IteratedCronModuleInterface) {
            if ($suspended) {
                $cronModuleService->wakeUp();
            }
            $inProgress = true;
            while ($this->canRun() && $inProgress === true) {
                $inProgress = $cronModuleService->iterate();
            }
            if ($inProgress === true) {
                $cronModuleService->sleep();
                return self::RUN_STATUS_SUSPENDED;
            } else {
                return self::RUN_STATUS_OK;
            }
        }
    }

    /**
     * @return bool
     */
    public function canRun()
    {
        return $this->canRunTo > new DateTimeImmutable();
    }
}
