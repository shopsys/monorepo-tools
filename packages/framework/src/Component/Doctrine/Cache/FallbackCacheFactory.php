<?php

namespace Shopsys\FrameworkBundle\Component\Doctrine\Cache;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\RedisCache;
use Exception;
use Redis;

class FallbackCacheFactory
{
    /**
     * @param \Redis $redis
     * @return \Doctrine\Common\Cache\CacheProvider
     */
    public function create(Redis $redis)
    {
        try {
            $redisCache = new RedisCache();
            $redisCache->setRedis($redis);

            return $redisCache;
        } catch (Exception $exception) {
        }

        return new ArrayCache();
    }
}
