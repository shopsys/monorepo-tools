<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Component\Redis;

use Redis;

class RedisFacade
{
    /**
     * @var \Redis[]
     */
    protected $allClients;

    /**
     * @var \Redis[]
     */
    protected $persistentClients;

    /**
     * @deprecated This property is deprecated since SSFW 7.3
     * @var \Redis[]
     */
    protected $cacheClients;

    /**
     * @param \Redis[] $allClients
     * @param \Redis[] $persistentClients
     */
    public function __construct(iterable $allClients, iterable $persistentClients = [])
    {
        $this->allClients = $allClients;
        $this->persistentClients = $persistentClients;
        $this->cacheClients = $this->getCacheClients();
    }

    /**
     * @return \Redis[]
     */
    protected function getCacheClients(): iterable
    {
        foreach ($this->allClients as $redis) {
            if (!in_array($redis, $this->persistentClients, true)) {
                yield $redis;
            }
        }
    }

    public function cleanCache(): void
    {
        foreach ($this->getCacheClients() as $redis) {
            $prefix = (string)$redis->getOption(Redis::OPT_PREFIX);
            $pattern = $prefix . '*';
            if (!$this->hasAnyKey($redis, $pattern)) {
                continue;
            }
            $redis->eval("return redis.call('del', unpack(redis.call('keys', ARGV[1])))", [$pattern]);
        }
    }

    public function pingAllClients(): void
    {
        foreach ($this->allClients as $redis) {
            $redis->ping();
        }
    }

    /**
     * @param \Redis $redis
     * @param string $pattern
     * @return bool
     */
    protected function hasAnyKey(Redis $redis, string $pattern): bool
    {
        $keyCount = $redis->eval("return table.getn(redis.call('keys', ARGV[1]))", [$pattern]);
        return (bool)$keyCount;
    }
}
