<?php

namespace Shopsys\FrameworkBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class ConfigureDomainsUrlsCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'shopsys:domains-urls:configure';

    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    private $localFilesystem;

    /**
     * @var string
     */
    private $configFilepath;

    /**
     * @param \Symfony\Component\Filesystem\Filesystem $localFilesystem
     * @param string $configFilepath
     */
    public function __construct(
        Filesystem $localFilesystem,
        string $configFilepath
    ) {
        $this->localFilesystem = $localFilesystem;
        $this->configFilepath = $configFilepath;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Copies domain URL configuration from .dist template if it\'s not set yet');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($this->localFilesystem->exists($this->configFilepath)) {
            $output->writeln('<fg=green>URLs for domains were already configured.</fg=green>');
        } else {
            $output->writeln('URLs for domains were not configured yet.');
            $this->localFilesystem->copy($this->configFilepath . '.dist', $this->configFilepath);
            $output->writeln(sprintf('<fg=green>Copied the default configuration into "%s".</fg=green>', $this->configFilepath));
        }
    }
}
