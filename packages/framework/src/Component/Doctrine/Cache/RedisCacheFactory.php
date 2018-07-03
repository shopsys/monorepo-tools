<?php

namespace Shopsys\FrameworkBundle\Component\Doctrine\Cache;

use Doctrine\Common\Cache\RedisCache;
use Redis;

class RedisCacheFactory
{
    /**
     * @param \Redis $redis
     * @return \Doctrine\Common\Cache\RedisCache
     */
    public function create(Redis $redis)
    {
        $redisCache = new RedisCache();
        $redisCache->setRedis($redis);

        return $redisCache;
    }
}
