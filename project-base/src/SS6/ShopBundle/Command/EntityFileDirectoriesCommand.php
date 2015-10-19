<?php

namespace SS6\ShopBundle\Command;

use SS6\ShopBundle\Component\UploadedFile\DirectoryStructureCreator;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class EntityFileDirectoriesCommand extends ContainerAwareCommand {

	protected function configure() {
		$this
			->setName('ss6:entity-file:directories')
			->setDescription('Create directories of entity files');
	}

	/**
	 * @param \Symfony\Component\Console\Input\InputInterface $input
	 * @param \Symfony\Component\Console\Output\OutputInterface $output
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {
		$output->writeln('Start of creating directories for entity files.');

		$entityFileDirectoryStructureCreator = $this->getContainer()->get(DirectoryStructureCreator::class);
		/* @var $entityFileDirectoryStructureCreator \SS6\ShopBundle\Component\UploadedFile\DirectoryStructureCreator */
		$entityFileDirectoryStructureCreator->makeFileDirectories();

		$output->writeln('<fg=green>Directories of entity files was successfully created.</fg=green>');
	}

}
