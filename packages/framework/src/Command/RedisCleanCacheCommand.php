<?php

namespace Shopsys\FrameworkBundle\Command;

use Shopsys\FrameworkBundle\Component\Redis\RedisFacade;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RedisCleanCacheCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'shopsys:redis:clean-cache';

    /**
     * @var \Shopsys\FrameworkBundle\Component\Redis\RedisFacade
     */
    private $redisFacade;

    /**
     * RedisCleanCacheCommand constructor.
     * @param \Shopsys\FrameworkBundle\Component\Redis\RedisFacade $redisFacade
     */
    public function __construct(RedisFacade $redisFacade)
    {
        $this->redisFacade = $redisFacade;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Cleans up redis cache');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->redisFacade->cleanCache();
    }
}
