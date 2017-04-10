<?php

namespace Shopsys\ShopBundle\Command;

use Shopsys\ShopBundle\Component\Domain\Config\DomainConfig;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ServerRunForDomainCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('shopsys:server:run')
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

        $domainConfig = $this->chooseDomainConfig($io);

        return $this->runServerForDomain($domainConfig, $output);
    }

    /**
     * @param \Symfony\Component\Console\Style\SymfonyStyle $io
     * @return \Shopsys\ShopBundle\Component\Domain\Config\DomainConfig
     */
    private function chooseDomainConfig(SymfonyStyle $io)
    {
        $domain = $this->getContainer()->get('shopsys.shop.component.domain');
        /* @var $domain \Shopsys\ShopBundle\Component\Domain\Domain */
        $domainConfigs = $domain->getAll();

        $firstDomainConfig = reset($domainConfigs);

        if (count($domainConfigs) === 1) {
            return $firstDomainConfig;
        }

        $domainChoices = [];
        foreach ($domainConfigs as $domainConfig) {
            $domainChoices[$domainConfig->getId()] = $domainConfig->getName();
        }
        $chosenDomainName = $io->choice(
            'There are more than one domain. Which domain do you want to use?',
            $domainChoices,
            $firstDomainConfig->getName()
        );
        foreach ($domainConfigs as $domainConfig) {
            if ($domainConfig->getName() === $chosenDomainName) {
                return $domainConfig;
            }
        }
    }

    /**
     * @param \Shopsys\ShopBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return int
     */
    private function runServerForDomain(DomainConfig $domainConfig, OutputInterface $output)
    {
        $command = $this->getApplication()->find('server:run');

        $url = $domainConfig->getUrl();
        $commandInput = new ArrayInput(['address' => preg_replace('~^https?://~', '', $url)]);

        return $command->run($commandInput, $output);
    }
}
