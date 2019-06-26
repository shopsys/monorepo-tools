<?php

namespace Shopsys\FrameworkBundle\Command;

use Shopsys\FrameworkBundle\Component\Redis\RedisFacade;
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
     * @var \Shopsys\FrameworkBundle\Component\Redis\RedisFacade
     */
    protected $redisFacade;

    /**
     * @param \Shopsys\FrameworkBundle\Component\Redis\RedisFacade $redisFacade
     */
    public function __construct(RedisFacade $redisFacade)
    {
        parent::__construct();

        $this->redisFacade = $redisFacade;
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
        try {
            $this->redisFacade->pingAllClients();
            $output->writeln('Redis is available');
        } catch (\RedisException $e) {
            throw new \Shopsys\FrameworkBundle\Command\Exception\RedisNotRunningException('Redis is not available.', 0, $e);
        }
    }
}
