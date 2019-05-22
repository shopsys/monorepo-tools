<?php

namespace Shopsys\FrameworkBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckRedisCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'shopsys:redis:check-availability';

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

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Checks availability of Redis');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Checks availability of Redis...');
        foreach ($this->cacheClients as $cacheClient) {
            try {
                $cacheClient->ping();
            } catch (\RedisException $e) {
                throw new \Shopsys\FrameworkBundle\Command\Exception\RedisNotRunningException('Redis is not available.');
            }
        }
        $output->writeln('Redis is available');
    }
}
