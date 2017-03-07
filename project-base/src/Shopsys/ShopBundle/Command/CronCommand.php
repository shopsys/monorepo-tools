<?php

namespace Shopsys\ShopBundle\Command;

use DateTime;
use DateTimeImmutable;
use Shopsys\ShopBundle\Component\Cron\Config\CronModuleConfig;
use Shopsys\ShopBundle\Component\Cron\CronFacade;
use Shopsys\ShopBundle\Component\Mutex\MutexFactory;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CronCommand extends ContainerAwareCommand
{
    const OPTION_MODULE = 'module';
    const OPTION_LIST = 'list';

    protected function configure()
    {
        $this
            ->setName('shopsys:cron')
            ->setDescription('Runs background jobs. Should be executed periodically by system CRON every 5 minutes.')
            ->addOption(self::OPTION_LIST, null, InputOption::VALUE_NONE, 'List all Service commands')
            ->addOption(self::OPTION_MODULE, null, InputOption::VALUE_OPTIONAL, 'Service ID');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $cronFacade = $this->getContainer()->get(CronFacade::class);
        /* @var $cronFacade \Shopsys\ShopBundle\Component\Cron\CronFacade */
        $mutexFactory = $this->getContainer()->get(MutexFactory::class);
        /* @var $mutexFactory \Shopsys\ShopBundle\Component\Mutex\MutexFactory */

        $optionList = $input->getOption(self::OPTION_LIST);
        if ($optionList === true) {
            $this->listAllCronModulesSortedByModuleId($output, $cronFacade);
        } else {
            $this->runCron($input, $cronFacade, $mutexFactory);
        }
    }

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param \Shopsys\ShopBundle\Component\Cron\CronFacade $cronFacade
     */
    private function listAllCronModulesSortedByModuleId(OutputInterface $output, CronFacade $cronFacade)
    {
        $cronModuleConfigs = $cronFacade->getAll();

        uasort($cronModuleConfigs, function (CronModuleConfig $cronModuleConfigA, CronModuleConfig $cronModuleConfigB) {
            return $cronModuleConfigA->getModuleId() > $cronModuleConfigB->getModuleId();
        });

        foreach ($cronModuleConfigs as $cronModuleConfig) {
            $output->writeln(sprintf('php bin/console shopsys:cron --module="%s"', $cronModuleConfig->getModuleId()));
        }
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Shopsys\ShopBundle\Component\Cron\CronFacade $cronFacade
     * @param \Shopsys\ShopBundle\Component\Mutex\MutexFactory $mutexFactory
     */
    private function runCron(InputInterface $input, CronFacade $cronFacade, MutexFactory $mutexFactory)
    {
        $moduleArgument = $input->getOption(self::OPTION_MODULE);
        if ($moduleArgument === null) {
            $cronFacade->scheduleModulesByTime($this->getCurrentRoundedTime());
        }

        $mutex = $mutexFactory->getCronMutex();
        if ($mutex->acquireLock(0)) {
            if ($moduleArgument === null) {
                $cronFacade->runScheduledModules();
            } else {
                $cronFacade->runModuleByModuleId($moduleArgument);
            }
            $mutex->releaseLock();
        } else {
            throw new \Shopsys\ShopBundle\Command\Exception\CronCommandException(
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
}
