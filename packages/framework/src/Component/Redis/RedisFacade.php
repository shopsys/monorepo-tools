<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Component\Redis;

use Redis;

class RedisFacade
{
    /**
     * @var \Redis[]
     */
    protected $cacheClients;

    /**
     * @param \Redis[] $cacheClients
     */
    public function __construct(array $cacheClients)
    {
        $this->cacheClients = $cacheClients;
    }

    public function cleanCache(): void
    {
        foreach ($this->cacheClients as $redis) {
            $prefix = (string)$redis->getOption(Redis::OPT_PREFIX);
            $pattern = $prefix . '*';
            $redis->eval("return redis.call('del', unpack(redis.call('keys', ARGV[1])))", [$pattern]);
        }
    }
}
