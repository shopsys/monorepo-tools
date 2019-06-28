<?php

declare(strict_types=1);

namespace Tests\FrameworkBundle\Unit\Command;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Redis;
use Shopsys\FrameworkBundle\Command\CheckRedisCommand;
use Shopsys\FrameworkBundle\Component\Redis\RedisFacade;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;

final class CheckRedisCommandTest extends TestCase
{
    /**
     * @return iterable
     */
    public function pingAllRedisClientsProvider(): iterable
    {
        yield [true, new RedisFacade([])];
        yield [true, new RedisFacade([$this->createRedisMockExpectingPing()])];
        yield [true, new RedisFacade([$this->createRedisMockExpectingPing(), $this->createRedisMockExpectingPing(), $this->createRedisMockExpectingPing()])];
        yield [false, new RedisFacade([$this->createRedisMockThrowingException()])];
        yield [false, new RedisFacade([$this->createRedisMockExpectingPing(), $this->createRedisMockThrowingException()])];
        yield [true, []];
        yield [true, [$this->createRedisMockExpectingPing()]];
        yield [true, [$this->createRedisMockExpectingPing(), $this->createRedisMockExpectingPing(), $this->createRedisMockExpectingPing()]];
        yield [false, [$this->createRedisMockThrowingException()]];
        yield [false, [$this->createRedisMockExpectingPing(), $this->createRedisMockThrowingException()]];
    }

    /**
     * @dataProvider pingAllRedisClientsProvider
     * @param bool $expectSuccess
     * @param \Shopsys\FrameworkBundle\Component\Redis\RedisFacade|\Redis[] $redisFacadeOrClients
     */
    public function testPingAllRedisClients(bool $expectSuccess, $redisFacadeOrClients): void
    {
        $checkRedisCommand = new CheckRedisCommand($redisFacadeOrClients);

        $output = new BufferedOutput();
        $returnCode = $checkRedisCommand->run(new StringInput(''), $output);

        $this->assertSame($expectSuccess ? 0 : 1, $returnCode);
        $this->assertStringContainsString($expectSuccess ? 'Redis is available' : 'Redis is not available', $output->fetch());
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    private function createRedisMockExpectingPing(): MockObject
    {
        $redisMock = $this->createMock(Redis::class);
        $redisMock->expects($this->once())->method('ping');

        return $redisMock;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    private function createRedisMockThrowingException(): MockObject
    {
        $redisMock = $this->createMock(Redis::class);
        $redisMock->method('ping')->willThrowException(new \RedisException());

        return $redisMock;
    }
}
