<?php

namespace SS6\ShopBundle\Command;

use SS6\ShopBundle\Component\Cron\CronFacade;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CronCommand extends ContainerAwareCommand {

	protected function configure() {
		$this
			->setName('ss6:cron')
			->setDescription('Maintenance service of ShopSys 6');
	}

	/**
	 * @param \Symfony\Component\Console\Input\InputInterface $input
	 * @param \Symfony\Component\Console\Output\OutputInterface $output
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {
		$cronFacade = $this->getContainer()->get(CronFacade::class);
		/* @var $cronFacade \SS6\ShopBundle\Component\Cron\CronFacade */

		$cronFacade->runServicesForTime($this->getActualRoundedTime());
	}

	private function getActualRoundedTime() {
		$time = new \DateTime(null);
		$time->modify('-' . $time->format('s') . ' sec');
		$time->modify('-' . ($time->format('i') % 5) . ' min');

		return $time;
	}

}
