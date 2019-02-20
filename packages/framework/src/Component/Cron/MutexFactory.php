<?php

namespace Shopsys\FrameworkBundle\Component\Cron;

use NinjaMutex\Lock\LockInterface;
use NinjaMutex\Mutex;

class MutexFactory
{
    const MUTEX_CRON_NAME = 'cron';

    /**
     * @var \NinjaMutex\Lock\LockInterface
     */
    protected $lock;

    /**
     * @var \NinjaMutex\Mutex[]
     */
    protected $mutexesByName;

    /**
     * @param \NinjaMutex\Lock\LockInterface $lock
     */
    public function __construct(LockInterface $lock)
    {
        $this->lock = $lock;
        $this->mutexesByName = [];
    }

    /**
     * @return \NinjaMutex\Mutex
     * @deprecated Use `getPrefixedCronMutex` instead
     */
    public function getCronMutex()
    {
        return $this->getMutexByName(self::MUTEX_CRON_NAME);
    }

    /**
     * @param string $prefix
     * @return \NinjaMutex\Mutex
     */
    public function getPrefixedCronMutex(string $prefix): Mutex
    {
        return $this->getMutexByName($prefix . '-' . self::MUTEX_CRON_NAME);
    }

    /**
     * @param string $mutexName
     * @return \NinjaMutex\Mutex
     */
    protected function getMutexByName(string $mutexName): Mutex
    {
        if (!array_key_exists($mutexName, $this->mutexesByName)) {
            $this->mutexesByName[$mutexName] = new Mutex($mutexName, $this->lock);
        }

        return $this->mutexesByName[$mutexName];
    }
}
