<?php

namespace SS6\ShopBundle\Command;

use Exception;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ZipArchive;

class ImageDemoCommand extends ContainerAwareCommand {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \Symfony\Component\Filesystem\Filesystem
	 */
	private $filesystem;

	protected function configure() {
		$this
			->setName('ss6:image:demo')
			->setDescription('Download demo images');
	}

	/**
	 * @param \Symfony\Component\Console\Input\InputInterface $input
	 * @param \Symfony\Component\Console\Output\OutputInterface $output
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {
		$this->filesystem = $this->getContainer()->get('filesystem');
		$this->em = $this->getContainer()->get('doctrine.orm.entity_manager');

		$archiveUrl = $this->getContainer()->getParameter('ss6.demo_images_archive_url');
		$dqlUrl = $this->getContainer()->getParameter('ss6.demo_images_dql_url');
		$cachePath = $this->getContainer()->getParameter('kernel.cache_dir');
		$localArchiveFilepath = $cachePath . DIRECTORY_SEPARATOR . 'demoImages.zip';
		$imagesPath = $this->getContainer()->getParameter('ss6.image_dir');

		if ($this->downloadImages($output, $archiveUrl, $localArchiveFilepath)) {
			if ($this->unpackImages($output, $imagesPath, $localArchiveFilepath)) {
				$this->loadDbChanges($output, $dqlUrl);
			}
		}

		$this->cleanUp($output, $localArchiveFilepath);
	}

	/**
	 * @param \Symfony\Component\Console\Output\OutputInterface $output
	 * @param string $imagesPath
	 * @param string $localArchiveFilepath
	 * @return bool
	 */
	private function unpackImages(OutputInterface $output, $imagesPath, $localArchiveFilepath) {
		$zipArchive = new ZipArchive();

		$result = $zipArchive->open($localArchiveFilepath);
		if ($result !== true) {
			$output->writeln('<fg=red>Unpacking of images archive failed</fg=red>');
			return false;
		}

		$zipArchive->extractTo($imagesPath);
		$zipArchive->close();
		$output->writeln('<fg=green>Unpacking of images archive was successfully completed</fg=green>');

		return true;
	}

	/**
	 * @param \Symfony\Component\Console\Output\OutputInterface $output
	 * @param string $dqlUrl
	 */
	private function loadDbChanges(OutputInterface $output, $dqlUrl) {
		$dqls = file_get_contents($dqlUrl);
		if ($dqls === false) {
			$output->writeln('<fg=red>Download of DB changes failed</fg=red>');
			return;
		}

		$dqls = explode(';', $dqls);
		$dqls = array_map('trim', $dqls);
		$dqls = array_filter($dqls);
		foreach ($dqls as $dql) {
			$this->em->createQuery($dql)->execute();
		}
		$output->writeln('<fg=green>DB changes were successfully applied (queries: ' . count($dqls) . ')</fg=green>');
	}

	/**
	 * @param \Symfony\Component\Console\Output\OutputInterface $output
	 * @param string $archiveUrl
	 * @param string $localArchiveFilepath
	 * @return bool
	 */
	private function downloadImages(OutputInterface $output, $archiveUrl, $localArchiveFilepath) {
		$output->writeln('Start downloading demo images');

		try {
			$this->filesystem->copy($archiveUrl, $localArchiveFilepath, true);
		} catch (Exception $e) {
			$output->writeln('<fg=red>Downloading of demo images failed</fg=red>');
			$output->writeln('<fg=red>Exception: ' . $e->getMessage() . '</fg=red>');

			return false;
		}

		$output->writeln('Success downloaded');
		return true;
	}

	/**
	 * @param \Symfony\Component\Console\Output\OutputInterface $output
	 * @param string $localArchiveFilepath
	 */
	private function cleanUp(OutputInterface $output, $localArchiveFilepath) {
		try {
			$this->filesystem->remove($localArchiveFilepath);
		} catch (Exception $e) {
			$output->writeln('<fg=red>Deleting of demo archive in cache failed</fg=red>');
			$output->writeln('<fg=red>Exception: ' . $e->getMessage() . '</fg=red>');
		}
	}

}
