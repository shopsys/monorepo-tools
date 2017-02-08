<?php

namespace SS6\ShopBundle\Command;

use SS6\ShopBundle\Component\Error\ErrorPagesFacade;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateErrorPagesCommand extends ContainerAwareCommand {

	protected function configure() {
		$this
			->setName('ss6:error-page:generate-all')
			->setDescription('Generates all error pages for production.');
	}

	/**
	 * @param \Symfony\Component\Console\Input\InputInterface $input
	 * @param \Symfony\Component\Console\Output\OutputInterface $output
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {
		$errorPagesFacade = $this->getContainer()->get(ErrorPagesFacade::class);
		/* @var $errorPagesFacade \SS6\ShopBundle\Component\Error\ErrorPagesFacade */

		$errorPagesFacade->generateAllErrorPagesForProduction();
	}

}
