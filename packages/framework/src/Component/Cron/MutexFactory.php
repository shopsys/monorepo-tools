<?php

namespace Shopsys\FrameworkBundle\Component\Cron;

use NinjaMutex\Lock\LockInterface;
use NinjaMutex\Mutex;

class MutexFactory
{
    protected const MUTEX_CRON_NAME = 'cron';

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
     * @param string $prefix
     * @return \NinjaMutex\Mutex
     */
    public function getPrefixedCronMutex(string $prefix): Mutex
    {
        return $this->getMutexByName($prefix . '-' . static::MUTEX_CRON_NAME);
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
