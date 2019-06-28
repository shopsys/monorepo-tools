<?php

namespace Shopsys\FrameworkBundle\Command;

use Redis;
use Shopsys\FrameworkBundle\Component\Redis\RedisFacade;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Webmozart\Assert\Assert;

class CheckRedisCommand extends Command
{
    protected const RETURN_CODE_OK = 0;
    protected const RETURN_CODE_ERROR = 1;

    /**
     * @var string
     */
    protected static $defaultName = 'shopsys:redis:check-availability';

    /**
     * @deprecated This property is deprecated since SSFW 7.3
     * @var \Redis[]
     */
    protected $cacheClients;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Redis\RedisFacade|null
     */
    protected $redisFacade;

    /**
     * @param \Shopsys\FrameworkBundle\Component\Redis\RedisFacade|\Redis[] $redisFacadeOrClients
     */
    public function __construct($redisFacadeOrClients)
    {
        parent::__construct();

        if ($redisFacadeOrClients instanceof RedisFacade) {
            $this->redisFacade = $redisFacadeOrClients;
            $this->cacheClients = [];
        } else {
            Assert::allIsInstanceOf($redisFacadeOrClients, Redis::class);

            @trigger_error(
                sprintf('Passing instances of "%s" directly into constructor of "%s" is deprecated since SSFW 7.3, pass "%s" instead', Redis::class, __CLASS__, RedisFacade::class),
                E_USER_DEPRECATED
            );

            $this->cacheClients = $redisFacadeOrClients;
        }
    }

    protected function configure()
    {
        $this
            ->setDescription('Checks availability of Redis');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $io->comment('Checks availability of Redis...');
        try {
            $this->pingAllClients();
            $io->success('Redis is available.');
        } catch (\RedisException $e) {
            $io->error('Redis is not available.');

            return static::RETURN_CODE_ERROR;
        }

        return static::RETURN_CODE_OK;
    }

    /**
     * @internal This method will be inlined when its implementation will be able to be simplified
     */
    protected function pingAllClients(): void
    {
        if ($this->redisFacade !== null) {
            $this->redisFacade->pingAllClients();
        } else {
            foreach ($this->cacheClients as $redisClient) {
                $redisClient->ping();
            }
        }
    }
}
