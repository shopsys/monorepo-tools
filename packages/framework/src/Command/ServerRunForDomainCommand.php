<?php

namespace Shopsys\FrameworkBundle\Command;

use Shopsys\FrameworkBundle\Component\Console\DomainChoiceHandler;
use Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ServerRunForDomainCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'shopsys:server:run';

    /**
     * @var \Shopsys\FrameworkBundle\Component\Console\DomainChoiceHandler
     */
    private $domainChoiceHelper;

    /**
     * @param \Shopsys\FrameworkBundle\Component\Console\DomainChoiceHandler $domainChoiceHelper
     */
    public function __construct(DomainChoiceHandler $domainChoiceHelper)
    {
        $this->domainChoiceHelper = $domainChoiceHelper;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Runs PHP built-in web server for a chosen domain');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        if ($input->hasParameterOption(['--env', '-e'])) {
            $io->error([
                'Environment passed in --env option is not supported.',
                'Environment can be set by file named DEVELOPMENT, PRODUCTION or TEST in project root.',
            ]);

            return 1;
        }

        $domainConfig = $this->domainChoiceHelper->chooseDomainConfig($io);

        return $this->runServerForDomain($domainConfig, $output);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return int
     */
    private function runServerForDomain(DomainConfig $domainConfig, OutputInterface $output)
    {
        $command = $this->getApplication()->find('server:run');

        $url = $domainConfig->getUrl();
        $commandInput = new ArrayInput(['addressport' => preg_replace('~^https?://~', '', $url)]);

        return $command->run($commandInput, $output);
    }
}
