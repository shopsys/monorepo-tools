<?php

namespace Shopsys\FrameworkBundle\Command;

use Shopsys\FrameworkBundle\Component\Redis\RedisFacade;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CheckRedisCommand extends Command
{
    protected const RETURN_CODE_OK = 0;
    protected const RETURN_CODE_ERROR = 1;

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
        $this->setDescription('Checks availability of Redis');
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
            $this->redisFacade->pingAllClients();
            $io->success('Redis is available.');
        } catch (\RedisException $e) {
            $io->error('Redis is not available.');

            return static::RETURN_CODE_ERROR;
        }

        return static::RETURN_CODE_OK;
    }
}
