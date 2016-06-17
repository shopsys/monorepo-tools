<?php

namespace SS6\ShopBundle\Command;

use DateTime;
use DateTimeImmutable;
use SS6\ShopBundle\Component\Cron\CronFacade;
use SS6\ShopBundle\Component\Mutex\MutexFactory;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CronCommand extends ContainerAwareCommand {

	const ARGUMENT_MODULE = 'module';

	protected function configure() {
		$this
			->setName('ss6:cron')
			->setDescription('Maintenance service of ShopSys 6')
			->addOption(self::ARGUMENT_MODULE, null, InputArgument::OPTIONAL, 'Service ID');
	}

	/**
	 * @param \Symfony\Component\Console\Input\InputInterface $input
	 * @param \Symfony\Component\Console\Output\OutputInterface $output
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {
		$cronFacade = $this->getContainer()->get(CronFacade::class);
		/* @var $cronFacade \SS6\ShopBundle\Component\Cron\CronFacade */
		$mutexFactory = $this->getContainer()->get(MutexFactory::class);
		/* @var $mutexFactory \SS6\ShopBundle\Component\Mutex\MutexFactory */

		$moduleArgument = $input->getOption(self::ARGUMENT_MODULE);
		$mutex = $mutexFactory->getCronMutex();
		if ($moduleArgument === null) {
			$cronFacade->scheduleModulesByTime($this->getCurrentRoundedTime());
		}

		if ($mutex->acquireLock(0)) {
			if ($moduleArgument === null) {
				$cronFacade->runScheduledModules();
			} else {
				$cronFacade->runModuleByModuleId($moduleArgument);
			}
			$mutex->releaseLock();
		} else {
			throw new \SS6\ShopBundle\Command\Exception\CronCommandException('Cron can run only one at this time');
		}

	}

	/**
	 * @return \DateTimeImmutable
	 */
	private function getCurrentRoundedTime() {
		$time = new DateTime(null);
		$time->modify('-' . $time->format('s') . ' sec');
		$time->modify('-' . ($time->format('i') % 5) . ' min');

		return DateTimeImmutable::createFromMutable($time);
	}

}
