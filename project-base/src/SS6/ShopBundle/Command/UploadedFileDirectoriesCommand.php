<?php

namespace SS6\ShopBundle\Command;

use SS6\ShopBundle\Component\UploadedFile\DirectoryStructureCreator;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UploadedFileDirectoriesCommand extends ContainerAwareCommand {

	protected function configure() {
		$this
			->setName('ss6:uploaded-file:directories')
			->setDescription('Create directories of UploadedFile');
	}

	/**
	 * @param \Symfony\Component\Console\Input\InputInterface $input
	 * @param \Symfony\Component\Console\Output\OutputInterface $output
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {
		$output->writeln('Start of creating directories for UploadedFile.');

		$uploadedFileDirectoryStructureCreator = $this->getContainer()->get(DirectoryStructureCreator::class);
		/* @var $uploadedFileDirectoryStructureCreator \SS6\ShopBundle\Component\UploadedFile\DirectoryStructureCreator */
		$uploadedFileDirectoryStructureCreator->makeUploadedFileDirectories();

		$output->writeln('<fg=green>Directories of UploadedFile was successfully created.</fg=green>');
	}

}
