<?php

declare(strict_types=1);

namespace Tests\ShopBundle\Functional\Component\Redis;

use Shopsys\FrameworkBundle\Component\Redis\RedisFacade;
use Tests\ShopBundle\Test\FunctionalTestCase;

class RedisFacadeTest extends FunctionalTestCase
{
    public function testCleanCache(): void
    {
        $redisClient = $this->getContainer()->get('snc_redis.test');
        $redisClient->set('test', 'test');
        $facade = new RedisFacade([$redisClient]);

        $facade->cleanCache();
        $this->assertSame(0, $redisClient->exists('test'));
    }

    public function testCleanCacheNoErrorOnEmpty(): void
    {
        $redisClient = $this->getContainer()->get('snc_redis.test');
        $facade = new RedisFacade([$redisClient]);

        $facade->cleanCache();
        $lastError = $redisClient->getLastError();
        $this->assertSame(null, $lastError);
    }
}
