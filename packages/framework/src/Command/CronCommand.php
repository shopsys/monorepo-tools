<?php

namespace Shopsys\FrameworkBundle\Command;

use DateTime;
use DateTimeImmutable;
use Shopsys\FrameworkBundle\Component\Cron\Config\CronModuleConfig;
use Shopsys\FrameworkBundle\Component\Cron\CronFacade;
use Shopsys\FrameworkBundle\Component\Cron\MutexFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CronCommand extends Command
{
    const OPTION_MODULE = 'module';
    const OPTION_LIST = 'list';
    private const OPTION_INSTANCE_NAME = 'instance-name';

    /**
     * @var string
     */
    protected static $defaultName = 'shopsys:cron';

    /**
     * @var \Shopsys\FrameworkBundle\Component\Cron\CronFacade
     */
    private $cronFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Cron\MutexFactory
     */
    private $mutexFactory;

    /**
     * @param \Shopsys\FrameworkBundle\Component\Cron\CronFacade $cronFacade
     * @param \Shopsys\FrameworkBundle\Component\Cron\MutexFactory $mutexFactory
     */
    public function __construct(
        CronFacade $cronFacade,
        MutexFactory $mutexFactory
    ) {
        $this->cronFacade = $cronFacade;
        $this->mutexFactory = $mutexFactory;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Runs background jobs. Should be executed periodically by system CRON every 5 minutes.')
            ->addOption(self::OPTION_LIST, null, InputOption::VALUE_NONE, 'List all Service commands')
            ->addOption(self::OPTION_MODULE, null, InputOption::VALUE_OPTIONAL, 'Service ID')
            ->addOption(self::OPTION_INSTANCE_NAME, null, InputOption::VALUE_REQUIRED, 'specific cron instance identifier');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $optionList = $input->getOption(self::OPTION_LIST);
        $optionInstanceName = $input->getOption(self::OPTION_INSTANCE_NAME);

        if ($optionList === true) {
            $this->listAllCronModulesSortedByServiceId($input, $output, $this->cronFacade);
        } else {
            $instanceName = $optionInstanceName ?? $this->chooseInstance($input, $output);

            $this->runCron($input, $this->cronFacade, $this->mutexFactory, $instanceName);
        }
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param \Shopsys\FrameworkBundle\Component\Cron\CronFacade $cronFacade
     */
    private function listAllCronModulesSortedByServiceId(InputInterface $input, OutputInterface $output, CronFacade $cronFacade)
    {
        $instanceNames = $cronFacade->getInstanceNames();
        $io = new SymfonyStyle($input, $output);

        if (count($instanceNames) === 1) {
            $cronModuleConfigs = $cronFacade->getAll();
            $io->text($this->getCronCommands($cronModuleConfigs));

            return;
        }

        foreach ($instanceNames as $instanceName) {
            $io->section($instanceName);

            $cronModuleConfigs = $cronFacade->getAllForInstance($instanceName);
            $io->text($this->getCronCommands($cronModuleConfigs, true));
        }
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Cron\Config\CronModuleConfig[] $cronModuleConfigs
     * @param bool $includeInstance
     * @return string[]
     */
    private function getCronCommands(array $cronModuleConfigs, bool $includeInstance = false): array
    {
        uasort($cronModuleConfigs, function (CronModuleConfig $cronModuleConfigA, CronModuleConfig $cronModuleConfigB) {
            return $cronModuleConfigA->getServiceId() > $cronModuleConfigB->getServiceId();
        });

        $commands = [];

        foreach ($cronModuleConfigs as $cronModuleConfig) {
            $command = sprintf('php bin/console %s --%s="%s"', $this->getName(), self::OPTION_MODULE, $cronModuleConfig->getServiceId());

            if ($includeInstance) {
                $command .= sprintf(' --%s=%s', self::OPTION_INSTANCE_NAME, $cronModuleConfig->getInstanceName());
            }

            $commands[] = $command;
        }

        return $commands;
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Shopsys\FrameworkBundle\Component\Cron\CronFacade $cronFacade
     * @param \Shopsys\FrameworkBundle\Component\Cron\MutexFactory $mutexFactory
     * @param string $instanceName
     */
    private function runCron(InputInterface $input, CronFacade $cronFacade, MutexFactory $mutexFactory, string $instanceName)
    {
        $requestedModuleServiceId = $input->getOption(self::OPTION_MODULE);
        $runAllModules = $requestedModuleServiceId === null;
        if ($runAllModules) {
            $cronFacade->scheduleModulesByTime($this->getCurrentRoundedTime());
        }

        $mutex = $mutexFactory->getPrefixedCronMutex($instanceName);
        if ($mutex->acquireLock(0)) {
            if ($runAllModules) {
                $cronFacade->runScheduledModulesForInstance($instanceName);
            } else {
                $cronFacade->runModuleByServiceId($requestedModuleServiceId);
            }
            $mutex->releaseLock();
        } else {
            throw new \Shopsys\FrameworkBundle\Command\Exception\CronCommandException(
                'Cron is locked. Another cron module is already running.'
            );
        }
    }

    /**
     * @return \DateTimeImmutable
     */
    private function getCurrentRoundedTime()
    {
        $time = new DateTime(null);
        $time->modify('-' . $time->format('s') . ' sec');
        $time->modify('-' . ($time->format('i') % 5) . ' min');

        return DateTimeImmutable::createFromMutable($time);
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return string
     */
    private function chooseInstance(InputInterface $input, OutputInterface $output): string
    {
        $instanceNames = $this->cronFacade->getInstanceNames();

        $defaultInstanceName = in_array(CronModuleConfig::DEFAULT_INSTANCE_NAME, $instanceNames, true) ? CronModuleConfig::DEFAULT_INSTANCE_NAME : reset($instanceNames);

        if (count($instanceNames) === 1) {
            return $defaultInstanceName;
        }

        $instanceNameChoices = [];
        foreach ($instanceNames as $instanceName) {
            $instanceNameChoices[] = $instanceName;
        }

        $io = new SymfonyStyle($input, $output);

        $chosenInstanceName = $io->choice(
            'There is more than one cron instance. Which instance do you want to use?',
            $instanceNameChoices,
            $defaultInstanceName
        );

        return $chosenInstanceName;
    }
}
