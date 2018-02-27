<?php

namespace Shopsys\ShopBundle\Command;

use DateTime;
use DateTimeImmutable;
use Shopsys\ShopBundle\Component\Cron\Config\CronModuleConfig;
use Shopsys\ShopBundle\Component\Cron\CronFacade;
use Shopsys\ShopBundle\Component\Mutex\MutexFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CronCommand extends Command
{
    const OPTION_MODULE = 'module';
    const OPTION_LIST = 'list';

    /**
     * @var string
     */
    protected static $defaultName = 'shopsys:cron';

    /**
     * @var \Shopsys\ShopBundle\Component\Cron\CronFacade
     */
    private $cronFacade;

    /**
     * @var \Shopsys\ShopBundle\Component\Mutex\MutexFactory
     */
    private $mutexFactory;

    /**
     * @param \Shopsys\ShopBundle\Component\Cron\CronFacade $cronFacade
     * @param \Shopsys\ShopBundle\Component\Mutex\MutexFactory $mutexFactory
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
            ->addOption(self::OPTION_MODULE, null, InputOption::VALUE_OPTIONAL, 'Service ID');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $optionList = $input->getOption(self::OPTION_LIST);
        if ($optionList === true) {
            $this->listAllCronModulesSortedByServiceId($output, $this->cronFacade);
        } else {
            $this->runCron($input, $this->cronFacade, $this->mutexFactory);
        }
    }

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param \Shopsys\ShopBundle\Component\Cron\CronFacade $cronFacade
     */
    private function listAllCronModulesSortedByServiceId(OutputInterface $output, CronFacade $cronFacade)
    {
        $cronModuleConfigs = $cronFacade->getAll();

        uasort($cronModuleConfigs, function (CronModuleConfig $cronModuleConfigA, CronModuleConfig $cronModuleConfigB) {
            return $cronModuleConfigA->getServiceId() > $cronModuleConfigB->getServiceId();
        });

        foreach ($cronModuleConfigs as $cronModuleConfig) {
            $output->writeln(sprintf('php bin/console shopsys:cron --module="%s"', $cronModuleConfig->getServiceId()));
        }
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Shopsys\ShopBundle\Component\Cron\CronFacade $cronFacade
     * @param \Shopsys\ShopBundle\Component\Mutex\MutexFactory $mutexFactory
     */
    private function runCron(InputInterface $input, CronFacade $cronFacade, MutexFactory $mutexFactory)
    {
        $requestedModuleServiceId = $input->getOption(self::OPTION_MODULE);
        $runAllModules = $requestedModuleServiceId === null;
        if ($runAllModules) {
            $cronFacade->scheduleModulesByTime($this->getCurrentRoundedTime());
        }

        $mutex = $mutexFactory->getCronMutex();
        if ($mutex->acquireLock(0)) {
            if ($runAllModules) {
                $cronFacade->runScheduledModules();
            } else {
                $cronFacade->runModuleByServiceId($requestedModuleServiceId);
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
